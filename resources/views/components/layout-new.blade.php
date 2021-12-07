<div class="container flex-grow-1 light-style container-p-y">
    <div class="container-m-nx container-m-ny bg-lightest mb-3">
        <div class="alert alert-primary">
            <h2 class="text-center"> Upload Files And Folders </h2>
         </div>
  
   
        <div class="file-manager-actions container-p-x py-2">
            <div>
                <form action="{{ route('file.store') }}" method="POST" id="fomrFiles" enctype="multipart/form-data">
                    @csrf
                    <label class="btn btn-outline-primary btn-lg mr-2" for="inputGroupFile02" aria-describedby="inputGroupFileAddon02">
                        <i class="far ion-md-cloud-upload"></i>&nbsp; 
                        Upload 
                    </label>
                    <input type="file" class="custom-file-input" id="inputGroupFile02" name="files[]" hidden multiple onchange="document.getElementById('fomrFiles').submit();">
                </form>
             </div>

            {{-- <div>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-default icon-btn md-btn-flat active"> <input type="radio" name="file-manager-view" value="file-manager-col-view" checked="" /> <span class="ion ion-md-apps"></span> </label>
                    <label class="btn btn-default icon-btn md-btn-flat"> <input type="radio" name="file-manager-view" value="file-manager-row-view" /> <span class="ion ion-md-menu"></span> </label>
                </div>
            </div> --}}

            <div>
                <button type="button" class="btn btn-outline-primary btn-lg " data-toggle="modal" data-target="#exampleModal">
                    Add New Folder
                </button>

                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header alert alert-primary">
                                <h5 class="modal-title " id="exampleModalLabel ">Add New Forder</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{route('file.store')}}" method="POST" id="newFolder">
                                    @csrf
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Name :</label>
                                        <input type="text" class="form-control" name="folder" id="Name folder">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="alert alert-danger" data-dismiss="modal">Close</button>
                                <button type="button" onclick="document.getElementById('newFolder').submit();" class="alert alert-primary">Save </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="m-0" />
    </div>
