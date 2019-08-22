@extends('admin.layouts.default_layout')
@section('content')
@section('title','Profile')
<section id="content">

        <!--start container-->
        <div class="container">

          <div id="profile-page" class="section">
            <!-- profile-page-header -->
            <div id="profile-page-header" class="card">
                <div class="card-image waves-effect waves-block waves-light">
                    <img class="activator" src="images/user-profile-bg.jpg" alt="user background">
                </div>
                <figure class="card-profile-image">
                    <img src="images/avatar.jpg" alt="profile image" class="circle z-depth-2 responsive-img activator">
                </figure>
                <div class="card-content">
                  <div class="row">
                    <div class="col s3 offset-s2">
                        <h4 class="card-title grey-text text-darken-4">Roger Waters</h4>
                        <p class="medium-small grey-text">Project Manager</p>
                    </div>
                    <div class="col s2 center-align">
                        <h4 class="card-title grey-text text-darken-4">10+</h4>
                        <p class="medium-small grey-text">Work Experience</p>
                    </div>
                    <div class="col s2 center-align">
                        <h4 class="card-title grey-text text-darken-4">6</h4>
                        <p class="medium-small grey-text">Completed Projects</p>
                    </div>
                    <div class="col s2 center-align">
                        <h4 class="card-title grey-text text-darken-4">$ 1,253,000</h4>
                        <p class="medium-small grey-text">Busness Profit</p>
                    </div>
                    <div class="col s1 right-align">
                      <a class="btn-floating activator waves-effect waves-light darken-2 right">
                          <i class="mdi-action-perm-identity"></i>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="card-reveal">
                    <p>
                      <span class="card-title grey-text text-darken-4">Roger Waters <i class="mdi-navigation-close right"></i></span>
                      <span><i class="mdi-action-perm-identity cyan-text text-darken-2"></i> Project Manager</span>
                    </p>

                    <p>I am a very simple card. I am good at containing small bits of information. I am convenient because I require little markup to use effectively.</p>

                    <p><i class="mdi-action-perm-phone-msg cyan-text text-darken-2"></i> +1 (612) 222 8989</p>
                    <p><i class="mdi-communication-email cyan-text text-darken-2"></i> mail@domain.com</p>
                    <p><i class="mdi-social-cake cyan-text text-darken-2"></i> 18th June 1990</p>
                    <p><i class="mdi-device-airplanemode-on cyan-text text-darken-2"></i> BAR - AUS</p>
                </div>
            </div>
            <!--/ profile-page-header -->

            <!-- profile-page-content -->
            <div id="profile-page-content" class="row">
              <!-- profile-page-sidebar-->
              <div id="profile-page-sidebar" class="col s12 m4">
                <!-- Profile About  -->
                <div class="card light-blue">
                  <div class="card-content white-text">
                    <span class="card-title">About Me!</span>
                    <p>I am a very simple card. I am good at containing small bits of information. I am convenient because I require little markup to use effectively.</p>
                  </div>
                </div>
                <!-- Profile About  -->

                <!-- Profile About Details  -->
                <ul id="profile-page-about-details" class="collection z-depth-1">
                  <li class="collection-item">
                    <div class="row">
                      <div class="col s5 grey-text darken-1"><i class="mdi-action-wallet-travel"></i> Project</div>
                      <div class="col s7 grey-text text-darken-4 right-align">ABC Name</div>
                    </div>
                  </li>
                  <li class="collection-item">
                    <div class="row">
                      <div class="col s5 grey-text darken-1"><i class="mdi-social-poll"></i> Skills</div>
                      <div class="col s7 grey-text text-darken-4 right-align">HTML, CSS</div>
                    </div>
                  </li>
                  <li class="collection-item">
                    <div class="row">
                      <div class="col s5 grey-text darken-1"><i class="mdi-social-domain"></i> Lives in</div>
                      <div class="col s7 grey-text text-darken-4 right-align">NY, USA</div>
                    </div>
                  </li>
                  <li class="collection-item">
                    <div class="row">
                      <div class="col s5 grey-text darken-1"><i class="mdi-social-cake"></i> Birth date</div>
                      <div class="col s7 grey-text text-darken-4 right-align">18th June, 1991</div>
                    </div>
                  </li>
                </ul>
                <!--/ Profile About Details  -->

                <!-- Profile About  -->
                <div class="card amber darken-2">
                  <div class="card-content white-text center-align">
                    <p class="card-title"><i class="mdi-social-group-add"></i> 3685</p>
                    <p>Followers</p>
                  </div>
                </div>
                <!-- Profile About  -->

                <!-- Profile feed  -->
                <ul id="profile-page-about-feed" class="collection z-depth-1">
                  <li class="collection-item avatar">
                    <img src="images/avatar.jpg" alt="" class="circle">
                    <span class="title">Project Title</span>
                    <p>Task assigned to new changes.
                      <br> <span class="ultra-small">Second Line</span>
                    </p>
                    <a href="#!" class="secondary-content"><i class="mdi-action-grade"></i></a>
                  </li>
                  <li class="collection-item avatar">
                    <i class="mdi-file-folder circle"></i>
                    <span class="title">New Project</span>
                    <p>First Line of Project Work
                      <br> <span class="ultra-small">Second Line</span>
                    </p>
                    <a href="#!" class="secondary-content"><i class="mdi-social-domain"></i></a>
                  </li>
                  <li class="collection-item avatar">
                    <i class="mdi-action-assessment circle green"></i>
                    <span class="title">New Payment</span>
                    <p>Last UK Project Payment
                      <br> <span class="ultra-small">$ 3,684.00</span>
                    </p>
                    <a href="#!" class="secondary-content"><i class="mdi-editor-attach-money"></i></a>
                  </li>
                  <li class="collection-item avatar">
                    <i class="mdi-av-play-arrow circle red"></i>
                    <span class="title">Latest News</span>
                    <p>company management news
                      <br> <span class="ultra-small">Second Line</span>
                    </p>
                    <a href="#!" class="secondary-content"><i class="mdi-action-track-changes"></i></a>
                  </li>
                </ul>
                <!-- Profile feed  -->

                <!-- task-card -->
                <ul id="task-card" class="collection with-header">
                  <li class="collection-header cyan">
                      <h4 class="task-card-title">My Task</h4>
                      <p class="task-card-date">March 26, 2015</p>
                  </li>
                  <li class="collection-item dismissable">
                      <input type="checkbox" id="task1" />
                      <label for="task1">Create Mobile App UI. <a href="#" class="secondary-content"><span class="ultra-small">Today</span></a>
                      </label>
                      <span class="task-cat teal">Mobile App</span>
                  </li>
                  <li class="collection-item dismissable">
                      <input type="checkbox" id="task2" />
                      <label for="task2">Check the new API standerds. <a href="#" class="secondary-content"><span class="ultra-small">Monday</span></a>
                      </label>
                      <span class="task-cat purple">Web API</span>
                  </li>
                  <li class="collection-item dismissable">
                      <input type="checkbox" id="task3" checked="checked" />
                      <label for="task3">Check the new Mockup of ABC. <a href="#" class="secondary-content"><span class="ultra-small">Wednesday</span></a>
                      </label>
                      <span class="task-cat pink">Mockup</span>
                  </li>
                  <li class="collection-item dismissable">
                      <input type="checkbox" id="task4" checked="checked" disabled="disabled" />
                      <label for="task4">I did it !</label>
                      <span class="task-cat cyan">Mobile App</span>
                  </li>
                </ul>
                <!-- task-card -->

                <!-- Profile Total sell -->
                <div class="card center-align">
                  <div class="card-content purple white-text">
                      <p class="card-stats-title"><i class="mdi-editor-attach-money"></i>Your Profit</p>
                      <h4 class="card-stats-number">$8990.63</h4>
                      <p class="card-stats-compare"><i class="mdi-hardware-keyboard-arrow-up"></i> 70% <span class="purple-text text-lighten-5">last month</span>
                      </p>
                  </div>
                  <div class="card-action purple darken-2">
                      <div id="sales-compositebar"></div>
                  </div>
                </div>

                <!-- flight-card -->
                <div id="flight-card" class="card">
                    <div class="card-header amber darken-2">
                        <div class="card-title">
                            <h4 class="flight-card-title">Your Next Flight</h4>
                            <p class="flight-card-date">June 18, Thu 04:50</p>
                        </div>
                    </div>
                    <div class="card-content-bg white-text">
                        <div class="card-content">
                            <div class="row flight-state-wrapper">
                                <div class="col s5 m5 l5 center-align">
                                    <div class="flight-state">
                                        <h4 class="margin">LDN</h4>
                                        <p class="ultra-small">London</p>
                                    </div>
                                </div>
                                <div class="col s2 m2 l2 center-align">
                                    <i class="mdi-device-airplanemode-on flight-icon"></i>
                                </div>
                                <div class="col s5 m5 l5 center-align">
                                    <div class="flight-state">
                                        <h4 class="margin">SFO</h4>
                                        <p class="ultra-small">San Francisco</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s6 m6 l6 center-align">
                                    <div class="flight-info">
                                        <p class="small"><span class="grey-text text-lighten-4">Depart:</span> 04.50</p>
                                        <p class="small"><span class="grey-text text-lighten-4">Flight:</span> IB 5786</p>
                                        <p class="small"><span class="grey-text text-lighten-4">Terminal:</span> B</p>
                                    </div>
                                </div>
                                <div class="col s6 m6 l6 center-align flight-state-two">
                                    <div class="flight-info">
                                        <p class="small"><span class="grey-text text-lighten-4">Arrive:</span> 08.50</p>
                                        <p class="small"><span class="grey-text text-lighten-4">Flight:</span> IB 5786</p>
                                        <p class="small"><span class="grey-text text-lighten-4">Terminal:</span> C</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- flight-card -->

                <!-- Map Card -->
                <div class="map-card">
                    <div class="card">
                        <div class="card-image waves-effect waves-block waves-light">
                            <div id="map-canvas" data-lat="40.747688" data-lng="-74.004142"></div>
                        </div>
                        <div class="card-content">
                            <a class="btn-floating activator btn-move-up waves-effect waves-light darken-2 right">
                                <i class="mdi-maps-pin-drop"></i>
                            </a>
                            <h4 class="card-title grey-text text-darken-4"><a href="#" class="grey-text text-darken-4">Company Name LLC</a>
                            </h4>
                            <p class="blog-post-content">Some more information about this company.</p>
                        </div>
                        <div class="card-reveal">
                            <span class="card-title grey-text text-darken-4">Company Name LLC <i class="mdi-navigation-close right"></i></span>
                            <p>Here is some more information about this company. As a creative studio we believe no client is too big nor too small to work with us to obtain good advantage.By combining the creativity of artists with the precision of engineers we develop custom solutions that achieve results.Some more information about this company.</p>
                            <p><i class="mdi-action-perm-identity cyan-text text-darken-2"></i> Manager Name</p>
                            <p><i class="mdi-communication-business cyan-text text-darken-2"></i> 125, ABC Street, New Yourk, USA</p>
                            <p><i class="mdi-action-perm-phone-msg cyan-text text-darken-2"></i> +1 (612) 222 8989</p>
                            <p><i class="mdi-communication-email cyan-text text-darken-2"></i> support@geekslabs.com</p>
                        </div>
                    </div>
                </div>
                <!-- Map Card -->

              </div>
              <!-- profile-page-sidebar-->

              <!-- profile-page-wall -->
              <div id="profile-page-wall" class="col s12 m8">
                <!-- profile-page-wall-share -->
                <div id="profile-page-wall-share" class="row">
                  <div class="col s12">
                    <ul class="tabs tab-profile z-depth-1 light-blue">
                      <li class="tab col s3"><a class="white-text waves-effect waves-light active" href="#UpdateStatus"><i class="mdi-editor-border-color"></i> Update Status</a>
                      </li>
                      <li class="tab col s3"><a class="white-text waves-effect waves-light" href="#AddPhotos"><i class="mdi-image-camera-alt"></i> Add Photos</a>
                      </li>
                      <li class="tab col s3"><a class="white-text waves-effect waves-light" href="#CreateAlbum"><i class="mdi-image-photo-album"></i> Create Album</a>
                      </li>
                    </ul>
                    <!-- UpdateStatus-->
                    <div id="UpdateStatus" class="tab-content col s12  grey lighten-4">
                      <div class="row">
                        <div class="col s2">
                          <img src="images/avatar.jpg" alt="" class="circle responsive-img valign profile-image-post">
                        </div>
                        <div class="input-field col s10">
                          <textarea id="textarea" row="2" class="materialize-textarea"></textarea>
                          <label for="textarea" class="">What's on your mind?</label>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col s12 m6 share-icons">
                          <a href="#"><i class="mdi-image-camera-alt"></i></a>
                          <a href="#"><i class="mdi-action-account-circle"></i></a>
                          <a href="#"><i class="mdi-hardware-keyboard-alt"></i></a>
                          <a href="#"><i class="mdi-communication-location-on"></i></a>
                        </div>
                        <div class="col s12 m6 right-align">
                           <!-- Dropdown Trigger -->
                            <a class='dropdown-button btn' href='#' data-activates='profliePost'><i class="mdi-action-language"></i> Public</a>

                            <!-- Dropdown Structure -->
                            <ul id='profliePost' class='dropdown-content'>
                              <li><a href="#!"><i class="mdi-action-language"></i> Public</a></li>
                              <li><a href="#!"><i class="mdi-action-face-unlock"></i> Friends</a></li>
                              <li><a href="#!"><i class="mdi-action-lock-outline"></i> Only Me</a></li>
                            </ul>

                            <a class="waves-effect waves-light btn"><i class="mdi-maps-rate-review left"></i>Post</a>
                        </div>
                      </div>
                    </div>
                    <!-- AddPhotos -->
                    <div id="AddPhotos" class="tab-content col s12  grey lighten-4">
                      <div class="row">
                        <div class="col s2">
                          <img src="images/avatar.jpg" alt="" class="circle responsive-img valign profile-image-post">
                        </div>
                        <div class="input-field col s10">
                          <textarea id="textarea" row="2" class="materialize-textarea"></textarea>
                          <label for="textarea" class="">Share your favorites photos!</label>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col s12 m6 share-icons">
                          <a href="#"><i class="mdi-image-camera-alt"></i></a>
                          <a href="#"><i class="mdi-action-account-circle"></i></a>
                          <a href="#"><i class="mdi-hardware-keyboard-alt"></i></a>
                          <a href="#"><i class="mdi-communication-location-on"></i></a>
                        </div>
                        <div class="col s12 m6 right-align">
                           <!-- Dropdown Trigger -->
                            <a class='dropdown-button btn' href='#' data-activates='profliePost2'><i class="mdi-action-language"></i> Public</a>

                            <!-- Dropdown Structure -->
                            <ul id='profliePost2' class='dropdown-content'>
                              <li><a href="#!"><i class="mdi-action-language"></i> Public</a></li>
                              <li><a href="#!"><i class="mdi-action-face-unlock"></i> Friends</a></li>
                              <li><a href="#!"><i class="mdi-action-lock-outline"></i> Only Me</a></li>
                            </ul>

                            <a class="waves-effect waves-light btn"><i class="mdi-maps-rate-review left"></i>Post</a>
                        </div>
                      </div>
                    </div>
                    <!-- CreateAlbum -->
                    <div id="CreateAlbum" class="tab-content col s12  grey lighten-4">
                      <div class="row">
                        <div class="col s2">
                          <img src="images/avatar.jpg" alt="" class="circle responsive-img valign profile-image-post">
                        </div>
                        <div class="input-field col s10">
                          <textarea id="textarea" row="2" class="materialize-textarea"></textarea>
                          <label for="textarea" class="">Create awesome album.</label>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col s12 m6 share-icons">
                          <a href="#"><i class="mdi-image-camera-alt"></i></a>
                          <a href="#"><i class="mdi-action-account-circle"></i></a>
                          <a href="#"><i class="mdi-hardware-keyboard-alt"></i></a>
                          <a href="#"><i class="mdi-communication-location-on"></i></a>
                        </div>
                        <div class="col s12 m6 right-align">
                           <!-- Dropdown Trigger -->
                            <a class='dropdown-button btn' href='#' data-activates='profliePost3'><i class="mdi-action-language"></i> Public</a>

                            <!-- Dropdown Structure -->
                            <ul id='profliePost3' class='dropdown-content'>
                              <li><a href="#!"><i class="mdi-action-language"></i> Public</a></li>
                              <li><a href="#!"><i class="mdi-action-face-unlock"></i> Friends</a></li>
                              <li><a href="#!"><i class="mdi-action-lock-outline"></i> Only Me</a></li>
                            </ul>

                            <a class="waves-effect waves-light btn"><i class="mdi-maps-rate-review left"></i>Post</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--/ profile-page-wall-share -->

                <!-- profile-page-wall-posts -->
                <div id="profile-page-wall-posts"class="row">
                  <div class="col s12">
                      <!-- medium -->
                      <div id="profile-page-wall-post" class="card">
                        <div class="card-profile-title">
                          <div class="row">
                            <div class="col s1">
                              <img src="images/avatar.jpg" alt="" class="circle responsive-img valign profile-post-uer-image">
                            </div>
                            <div class="col s10">
                              <p class="grey-text text-darken-4 margin">John Doe</p>
                              <span class="grey-text text-darken-1 ultra-small">Shared publicly  -  26 Jun 2015</span>
                            </div>
                            <div class="col s1 right-align">
                              <i class="mdi-navigation-expand-more"></i>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col s12">
                              <p>I am a very simple wall post. I am good at containing <a href="#">#small</a> bits of <a href="#">#information</a>.  I require little more information to use effectively.</p>
                            </div>
                          </div>
                        </div>
                        <div class="card-image profile-medium">
                          <img src="images/gallary/2.jpg" alt="sample" class="responsive-img profile-post-image profile-medium">
                          <span class="card-title">Card Title</span>
                        </div>
                        <div class="card-content">
                          <p>I am a very simple card. I am good at containing small bits of information. I am convenient because I require little markup to use effectively.</p>
                        </div>
                        <div class="card-action row">
                          <div class="col s4 card-action-share">
                            <a href="#">Like</a>
                            <a href="#">Share</a>
                          </div>

                          <div class="input-field col s8 margin">
                            <input id="profile-comments" type="text" class="validate margin">
                            <label for="profile-comments" class="">Comments</label>
                          </div>
                        </div>
                      </div>

                      <!-- medium video -->
                      <div id="profile-page-wall-post" class="card">
                        <div class="card-profile-title">
                          <div class="row">
                            <div class="col s1">
                              <img src="images/avatar.jpg" alt="" class="circle responsive-img valign profile-post-uer-image">
                            </div>
                            <div class="col s10">
                              <p class="grey-text text-darken-4 margin">John Doe</p>
                              <span class="grey-text text-darken-1 ultra-small">Shared publicly  -  26 Jun 2015</span>
                            </div>
                            <div class="col s1 right-align">
                              <i class="mdi-navigation-expand-more"></i>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col s12">
                              <p>I am a very simple wall post. I am good at containing <a href="#">#small</a> bits of <a href="#">#information</a>.  I require little more information to use effectively.</p>
                            </div>
                          </div>
                        </div>
                        <div class="card-image profile-medium">
                          <div class="video-container no-controls">
                            <iframe width="640" height="360" src="https://www.youtube.com/embed/10r9ozshGVE" frameborder="0" allowfullscreen></iframe>
                          </div>
                          <span class="card-title">Card Title</span>
                        </div>
                        <div class="card-content">
                          <p>I am a very simple card. I am good at containing small bits of information. I am convenient because I require little markup to use effectively.</p>
                        </div>
                        <div class="card-action row">
                          <div class="col s4 card-action-share">
                            <a href="#">Like</a>
                            <a href="#">Share</a>
                          </div>

                          <div class="input-field col s8 margin">
                            <input id="profile-comments" type="text" class="validate margin">
                            <label for="profile-comments" class="">Comments</label>
                          </div>
                        </div>
                      </div>

                      <!-- small -->
                      <div id="profile-page-wall-post" class="card">
                        <div class="card-profile-title">
                          <div class="row">
                            <div class="col s1">
                              <img src="images/avatar.jpg" alt="" class="circle responsive-img valign profile-post-uer-image">
                            </div>
                            <div class="col s10">
                              <p class="grey-text text-darken-4 margin">John Doe</p>
                              <span class="grey-text text-darken-1 ultra-small">Shared publicly  -  26 Jun 2015</span>
                            </div>
                            <div class="col s1 right-align">
                              <i class="mdi-navigation-expand-more"></i>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col s12">
                              <p>I am a very simple wall post. I am good at containing <a href="#">#small</a> bits of <a href="#">#information</a>.  I require little more information to use effectively.</p>
                            </div>
                          </div>
                        </div>
                        <div class="card-image profile-small">
                          <img src="images/gallary/1.jpg" alt="sample" class="responsive-img profile-post-image">
                          <span class="card-title">Card Title</span>
                        </div>
                        <div class="card-content">
                          <p>I am a very simple card. I am good at containing small bits of information. I am convenient because I require little markup to use effectively.</p>
                        </div>
                        <div class="card-action row">
                          <div class="col s4 card-action-share">
                            <a href="#">Like</a>
                            <a href="#">Share</a>
                          </div>

                          <div class="input-field col s8 margin">
                            <input id="profile-comments" type="text" class="validate">
                            <label for="profile-comments" class="">Comments</label>
                          </div>
                        </div>
                      </div>

                      <!-- small -->
                      <div id="profile-page-wall-post" class="card">
                        <div class="card-profile-title">
                          <div class="row">
                            <div class="col s1">
                              <img src="images/avatar.jpg" alt="" class="circle responsive-img valign profile-post-uer-image">
                            </div>
                            <div class="col s10">
                              <p class="grey-text text-darken-4 margin">John Doe</p>
                              <span class="grey-text text-darken-1 ultra-small">Shared publicly  -  26 Jun 2015</span>
                            </div>
                            <div class="col s1 right-align">
                              <i class="mdi-navigation-expand-more"></i>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col s12">
                              <p>I am a very simple wall post. I am good at containing <a href="#">#small</a> bits of <a href="#">#information</a>.  I require little more information to use effectively.</p>
                            </div>
                          </div>
                        </div>
                        <div class="card-image profile-large">
                          <img src="images/gallary/3.jpg" alt="sample" class="responsive-img profile-post-image">
                          <span class="card-title">Card Title</span>
                        </div>
                        <div class="card-content">
                          <p>I am a very simple card. I am good at containing small bits of information. I am convenient because I require little markup to use effectively.</p>
                        </div>
                        <div class="card-action row">
                          <div class="col s4 card-action-share">
                            <a href="#">Like</a>
                            <a href="#">Share</a>
                          </div>

                          <div class="input-field col s8 margin">
                            <input id="profile-comments" type="text" class="validate">
                            <label for="profile-comments" class="">Comments</label>
                          </div>
                        </div>
                      </div>
                  </div>
                </div>
                <!--/ profile-page-wall-posts -->

              </div>
              <!--/ profile-page-wall -->

            </div>
          </div>
        </div>
        </div>
        <!--end container-->
      </section>
@stop
