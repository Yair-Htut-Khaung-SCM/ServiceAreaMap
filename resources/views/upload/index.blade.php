@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header"> Detailed Simulation Map </div>
            <div class="card-body">
            @if (session('mesh_flash_message'))
                <p class="flash_message">
                    {{ session('mesh_flash_message') }}
                </p>
            @endif
                <ul class="list-group">
                    <li class="list-group-item p-4 pb-0">
                        <form method="POST" action="{{ route('upload.meshcsv') }}" class="" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="meshcsv"> Mesh Code List </label> <br>
                                    <small class="csv-warn text-muted"> Upload format: CSV (UTF-8) </small>
                                </div>
                                <div id="file" class="col-md-6 d-flex justify-content-end">
                                    <input type="file" class="form-control-file mt-3" id="meshcsv" name="meshcsv">
                                    <div class="btnWrap">
                                        <button type="submit" class="upload-csv">
                                            Upload <i class="fa fa-upload"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header"> Detailed Simulation Map Generator </div>
            <div class="d-flex card-body justify-content-between col-12">
              <div class="wrapper col-6 mr-4">
                  <div class="tabs-wrapper">
                      <ul class="nav nav-tabs">
                        <li><a data-toggle="tab" href="#menu1">One point</a></li>
                        <li><a data-toggle="tab" href="#menu2">Multi point</a></li>
                        <li  class="active"><a data-toggle="tab" href="#menu3">Center point</a></li>
                      </ul>
                  </div>
                  <div class="tab-content">
                    <div id="menu1" class="tab-pane fade col-12">
                      <div class="d-flex">
                          <form id="meshConverterForm" class="col-8">
                              <h4 class="mt-5 mb-5">Enter Latitude and Longitude</h4>
                              <div class="form-group col-9 mb-4">
                                  <label for="latitude" class="col-6">Latitude:</label>
                                  <input type="text" class="form-control" id="latitude" placeholder="sample 16.765" required pattern="\d{1,2}\.\d{3}">
                                  <div class="invalid-feedback">Please enter a valid latitude (e.g., 16.765).</div>
                              </div>
                              <div class="form-group col-9 mb-4">
                                  <label for="longitude" class="col-6">Longitude:</label>
                                  <input type="text" class="form-control" id="longitude" placeholder="sample 96.164" required pattern="\d{1,2}\.\d{3}">
                                  <div class="invalid-feedback">Please enter a valid longitude (e.g., 96.164).</div>
                              </div>
                              <div class="form-group col-10">
                                  <label for="locationType" class="col-5">Location Type:</label>
                                  <select class="form-select me-1 col-1" id="locationType">
                                    <option value="landStable">Land Stable</option>
                                    <option value="landNearLimit">Land Near Limit</option>
                                    <option value="seaStable">Sea Stable</option>
                                    <option value="seaNearLimit">Sea Near Limit</option>
                                  </select>
                                    <input type="color" class="col-2 border-0" id="colorPicker" value="#ff0000">
                                </div>
                                <div class="text-danger" id="invalid-onepoint"></div>
                              <button type="button" class="btn btn-primary mt-5" onclick="convertToMeshCode()">Convert to Mesh Code  <i class="fa fa-arrow-right"></i></button>
                          </form>
                          <div class="mt-6 col-4">
                              <h4  style="margin-top:30px">Mesh Code Result</h4>
                              {{-- <span id="meshCodeResult"></span> --}}
                              <div class="clipboard">
                                  <input style="display: none" id="copy-text" value="">
                                  <span onclick="copy()" class="copy-input mt-5 mb-3" id="meshCodeResult" readonly></span>
                                  <button class="copy-btn" id="copyButton" onclick="copy()"><i class="far fa-copy"></i></button>
                                  
                                  </div>
                                  <div id="copied-success" class="copied">
                                    <span>copied!</span>
                              </div>
                              <button class="btn btn-primary mt-2" id="addListBtn">Add to List <i class="fa fa-file"></i></button>
                          </div>
                      </div>
                    </div>
                    <div id="menu2" class="tab-pane fade">
                      <div class="d-flex">
                          <form id="meshConverterForm" class="col-8">
                              <h4 class="mt-5 mb-5">Enter Latitude and Longitude</h4>
                              <div class="form-group col-11 mb-4">
                                  <label class="multi-label" for="latitude" class="col-1">y-axis :</label>
                                  <div class="col-4 d-flex multi-input">
                                      <input type="text" class="form-control" id="y-lat" placeholder="lat" required pattern="\d{1,2}\.\d{3}">
                                      <input type="text" class="form-control" id="y-lon" placeholder="lon" required pattern="\d{1,2}\.\d{3}">
                                  </div>
      
                                  <label class="multi-label" for="latitude" class="col-1">y-axis<br> end :</label>
                                  <div class="col-4 d-flex multi-input">
                                      <input type="text" class="form-control" id="y-end-lat" placeholder="lat" required pattern="\d{1,2}\.\d{3}">
                                      <input type="text" class="form-control" id="y-end-lon" placeholder="lon" required pattern="\d{1,2}\.\d{3}">
                                  </div>
                                  <div class="invalid-feedback">Please enter a valid latitude (e.g., 16.765).</div>
                              </div>
                              <div class="form-group col-11 mb-4">
                                  <label class="multi-label" for="latitude" class="col-1">x-axis :</label>
                                  <div class="col-4 d-flex multi-input">
                                      <input type="text" class="form-control" id="x-lat" placeholder="lat" required pattern="\d{1,2}\.\d{3}">
                                      <input type="text" class="form-control" id="x-lon" placeholder="lon" required pattern="\d{1,2}\.\d{3}">
                                  </div>
      
                                  <label class="multi-label" for="latitude" class="col-1">x-axis<br> end :</label>
                                  <div class="col-4 d-flex multi-input">
                                      <input type="text" class="form-control" id="x-end-lat" placeholder="lat" required pattern="\d{1,2}\.\d{3}">
                                      <input type="text" class="form-control" id="x-end-lon" placeholder="lon" required pattern="\d{1,2}\.\d{3}">
                                  </div>
                                  <div class="invalid-feedback">Please enter a valid latitude (e.g., 16.765).</div>
                              </div>
                              <div class="form-group col-8">
                                  <label for="locationType" class="multi-label col-5">Location Type:</label>
                                  <select class="form-select multi-label me-1" id="multLocationType">
                                    <option value="landStable">Land Stable</option>
                                    <option value="landNearLimit">Land Near Limit</option>
                                    <option value="seaStable">Sea Stable</option>
                                    <option value="seaNearLimit">Sea Near Limit</option>
                                  </select>
                                  <input type="color" class="col-2 border-0" id="colorPickerMultiLocation" value="#ff0000">
                                </div>
                                <div class="text-danger" id='invalid-mulpoint'></div>
                              <button type="button" class="btn btn-primary mt-5" onclick="convertMultiToMeshCode()">Convert to Mesh Code  <i class="fa fa-arrow-right"></i></button>
                          </form>
                          <div class="mt-6 col-4">
                              <h4  style="margin-top:30px">Mesh Code Result</h4>
                              <div class="clipboard">
                                  <input style="display: none" id="copy-text" value="">
                                  <textarea onclick="copy()" class="copy-input copy-input-multi mt-5 mb-3" id="multiMeshCodeResult" readonly></textarea>
                                  <button class="copy-btn" id="copyButton" onclick="copy()"><i class="far fa-copy"></i></button>
                                  
                                  </div>
                                  <div id="copied-success" class="copied">
                                    <span>copied!</span>
                              </div>
                              <button class="btn btn-primary mt-2" id="addMultListBtn">Add to List <i class="fa fa-file"></i></button>
                          </div>
                      </div>
                    </div>
                     <div id="menu3" class="tab-pane fade  in active col-12">
                      <div class="d-flex">
                          <form id="meshConverterForm" class="col-8">
                              <h4 class="mt-5 mb-5">Enter Latitude and Longitude</h4>
                              <div class="form-group col-8 mb-4"  style="margin-top: 43px">
                                  <label class="multi-label" for="latitude" class="col-1">Center Point :</label>
                                  <div class="col-6 d-flex center-outer">
                                      <input type="text" class="form-control" id="center-lat" placeholder="lat" required pattern="\d{1,2}\.\d{3}">
                                      <input type="text" class="form-control" id="center-lon" placeholder="lon" required pattern="\d{1,2}\.\d{3}">
                                  </div>
                                  <div class="invalid-feedback">Please enter a valid latitude (e.g., 16.765).</div>
                              </div>
                              <div class="form-group col-8 mb-4">
                                  <label class="multi-label" for="latitude" class="col-1">Outer Point :</label>
                                  <div class="col-6 d-flex center-outer">
                                      <input type="text" class="form-control" id="outer-lat" placeholder="lat" required pattern="\d{1,2}\.\d{3}">
                                      <input type="text" class="form-control" id="outer-lon" placeholder="lon" required pattern="\d{1,2}\.\d{3}">
                                  </div>
      
                                  <div class="invalid-feedback">Please enter a valid latitude (e.g., 16.765).</div>
                              </div>
                              <div class="form-group col-8">
                                  <label for="locationType" class="multi-label col-5">Location Type:</label>
                                  <select class="form-select multi-label" id="centerLocationType" style="margin-left:-10px">
                                    <option value="landStable">Land Stable</option>
                                    <option value="landNearLimit">Land Near Limit</option>
                                    <option value="seaStable">Sea Stable</option>
                                    <option value="seaNearLimit">Sea Near Limit</option>
                                  </select>
                                  <input type="color" class="col-2 border-0" id="colorPickerCenterLocation" value="#ff0000">
                                </div>
                                <div class="text-danger" id='invalid-centerpoint'></div>
                              <button type="button" class="btn mt-5" onclick="convertCenterToMeshCode()">Convert to Mesh Code <i class="fa fa-arrow-right"></i></button>
                          </form>
                          <div class="mt-6 col-4">
                              <h4  style="margin-top:30px">Mesh Code Result</h4>
                              <div class="clipboard">
                                  <input style="display: none" id="copy-text" value="">
                                  <textarea onclick="copy()" class="copy-input copy-input-multi mt-5 mb-3" id="centerMeshCodeResult" readonly></textarea>
                                  <button class="copy-btn" id="copyButton" onclick="copy()"><i class="far fa-copy"></i></button>
                                  
                                  
                                  </div>
                                  <div id="copied-success" class="copied">
                                    <span>copied!</span>
                              </div>
                              <button class="btn btn-primary mt-2" id="addCenterListBtn">Add to List <i class="fa fa-file"></i></button>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
      
              <div class="col-5">
                  <div class="clearfix">
                      <button class="btn btn-success mt-2 me-3 float-start" id="exportBtn">Export to CSV <i class="fa fa-download"></i></button>
                      <button id="deleteAllBtn" class="btn btn-danger mt-2 float-start">Clear <i class="fa fa-trash"></i></button>

                  </div>
      

                  <ul class="d-flex table-header">
                      <li>Mesh Code</li>
                      <li>Location Type</li>
                      <li>Service Color</li>
                  </ul>
                  <div style="overflow-y:scroll; height:300px;">
                      <table class="table">
      
                          <tbody id="meshCodeList">
                          <!-- Mesh Code rows will be appended here -->
                          </tbody>
      
                      </table>
                  </div>
              </div>
      
            </div>
        </div>


  

  </div>

@endsection