 <div class="col-md-9">
     <!-- Tab Content -->
     <div class="tab-content">
         <!-- Public Disk - Available to all authenticated users -->
         <x-admin.media-manager.disk-content disk-id="public" disk-name="public" icon="fas fa-cloud-upload-alt"
             :is-active="true">
             Supports images, documents, and other file types accessible to all users
         </x-admin.media-manager.disk-content>

         <!-- Private Disk - Available to Instructor level and higher -->
         @if (auth('admin')->check() && auth('admin')->user()->IsInstructor())
             <x-admin.media-manager.disk-content disk-id="private" disk-name="local" icon="fas fa-shield-alt">
                 Secure storage for admin and protected content
             </x-admin.media-manager.disk-content>
         @endif

         <!-- S3 Archive - Only available to Administrator level and higher -->
         @if (auth('admin')->check() && auth('admin')->user()->IsAdministrator())
             <x-admin.media-manager.disk-content disk-id="s3" disk-name="s3" icon="fas fa-cloud">
                 Long-term storage and backup
             </x-admin.media-manager.disk-content>
         @endif
     </div>
 </div>
