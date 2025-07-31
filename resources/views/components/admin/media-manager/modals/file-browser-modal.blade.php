 <div class="modal fade" id="fileBrowserModal" tabindex="-1" role="dialog">
     <div class="modal-dialog modal-xl" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title">
                     <i class="fas fa-folder-open"></i> File Browser - <span id="modal-category-name"></span>
                 </h4>
                 <button type="button" class="close" data-dismiss="modal">
                     <span>&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <div id="file-browser-content">
                     <div class="text-center">
                         <i class="fas fa-spinner fa-spin fa-3x"></i>
                         <p>Loading files...</p>
                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-danger" id="delete-selected">
                     <i class="fas fa-trash"></i> Delete Selected
                 </button>
             </div>
         </div>
     </div>
 </div>
