<template>
  <div class="flex w-full justify-end items-center mx-3">   
    <button
      data-testid="action-confirm"
      dusk="run-action-button"
      v-for="(action, index) in actions" 
      :key="action.uriKey + index"
      @click.prevent="runAction(action)" 
      class="btn btn-default btn-primary flex items-center justify-center px-3"  
      :title="action.name"
    >{{
      action.name
    }}</button>   

    <!-- Action Confirmation Modal -->
    <portal to="modals" transition="fade-transition">
      <component
        v-if="confirmActionModalOpened"
        class="text-left"
        :is="selectedAction.component"
        :working="working"
        :selected-resources="selectedResources"
        :resource-name="resourceName"
        :action="selectedAction"
        :errors="errors"
        @confirm="executeAction"
        @close="closeConfirmationModal"
      />
    </portal>
  </div>
</template>

<script>
import HandlesActions from './../mixins/HandlesActions'
import { Errors, InteractsWithResourceInformation } from 'laravel-nova'

export default {
  mixins: [InteractsWithResourceInformation, HandlesActions], 
  props: {
    resourceName: String, 
  }, 
  created() {
    this.getActions()
  },
  methods: { 
    /**
     * Get the actions available for the current resource.
     */
    getActions() { 
      return Nova.request()
        .get(`/nova-api/${this.resourceName}/actions`, {
          params: { 
          },
        })
        .then(response => {
          this.actions = _.filter(response.data.actions, a => a.showOnToolbar)
          this.pivotActions = response.data.pivotActions   
        })
    }, 

    runAction(action) {
      this.selectedAction = action;
      this.selectedActionKey = action.uriKey
      this.determineActionStrategy()
    }
  },

  watch: {
    /**
     * Watch the actions property for changes.
     */
    actions(actions) { 
      this.selectedActionKey = ''
      this.initializeActionFields() 
    },

    /**
     * Watch the pivot actions property for changes.
     */
    pivotActions() {
      this.selectedActionKey = ''
      this.initializeActionFields()
    },
  },
}
</script> 
