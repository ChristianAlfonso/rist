<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> 
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="landing-page">

    <nav class="navbar navbar-expand-xl navbar-light px-2 py-3 fixed-top bg-light shadow shadow-sm">
        <div class="navbar-brand d-flex align-items-center justify-content-center">
            <img src="images/logo.png" alt="logo"> &nbsp;
            <h4 class="text-danger">EULOGIO RODRIGUEZ VOCATIONAL HIGH SCHOOL</h2>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
          </button>


        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="nav ms-auto">
                <li class="nav-item">
                    <a href="#home" class="nav-link text-danger">HOME</a>
                </li>
                <li class="nav-item">
                    <a href="#announcement" class="nav-link text-danger">ANNOUNCEMENT</a>
                </li>
                <li class="nav-item">
                    <a href="#about" class="nav-link text-danger">ABOUT US</a>
                </li>   
                <li class="nav-item">
                    <a href="#contact" class="nav-link text-danger">CONTACT</a>
                </li>
                <li class="nav-item">
                    <a href="login.php" class="nav-link text-danger">LOGIN</a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="hero-section" id="home">
        <div class="hero-content container p-5">
            <div class="hero-title text-center text-light">
                <span class="display-4">ONLINE ACADEMIC PERFORMANCE MONITORING</span>
            </div>
            <div class="hero-button mt-5 justify-content-center d-flex">
                <a href="https://www.facebook.com/EarvhsOfficial" type="button" class="btn btn-outline-light w-25">VISIT US</a>
            </div>
        </div>
    </main>

    <section class="announcement-section bg-light p-5 " id="announcement">

        <div class="display-3 mt-5 text-danger">
            Annoucement
        </div> 

       
        <div class="news mt-3">

            <div class="card shadow p-2">
                <div class="card-title h3 text-center">
                    lorem
                </div>

                <div class="card-body">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatibus minus reiciendis delectus exercitationem tempora, distinctio a nobis eius velit consectetur repellendus est similique debitis impedit enim officia maiores. Aperiam aspernatur unde nulla alias, eum perspiciatis et excepturi voluptatem quaerat, sint eos recusandae facere, repellendus delectus itaque ex distinctio officiis laudantium.
                </div>
                
                <div class="card-bottom p-2 ">
                    <a href="#" class="btn btn-outline-danger">Read More</a>
                </div>


            </div>

            <div class="card shadow p-2">
                <div class="card-title h3 text-center">
                    lorem
                </div>

                <div class="card-body">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi, ut blanditiis explicabo quas amet consectetur eos vitae iure ad praesentium eum vel odio minus esse, fugit laboriosam! Consequatur mollitia quas quaerat numquam voluptas rem dolor aperiam error optio saepe?
                </div>

                <div class="card-bottom p-2 ">
                    <a href="#" class="btn btn-outline-danger">Read More</a>
                </div>
            </div>
            
            <div class="card shadow p-2">
                <div class="card-title h3 text-center">
                    lorem
                </div>

                <div class="card-body">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi, ut blanditiis explicabo quas amet consectetur eos vitae iure ad praesentium eum vel odio minus esse, fugit laboriosam! Consequatur mollitia quas quaerat numquam voluptas rem dolor aperiam error optio saepe?
                </div>

                <div class="card-bottom p-2 ">
                    <a href="#" class="btn btn-outline-danger">Read More</a>
                </div>
            </div>     

        

            
        </div>
  

    </section>

    <section class="about-section p-5" id="about">
        <div class="display-3 mt-5 text-light">
            Get to Know Us
        </div> 

        <div class="about-content">
            <!-- Carousel -->
            <div id="demo" class="carousel slide shadow" data-bs-ride="carousel">

                <!-- Indicators/dots -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
                </div>
    
                <!-- The slideshow/carousel -->
                <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="images/announcement-1.jpg" class="img-fluid">
                        <div class="carousel-caption">
                            <h3>lorem</h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Debitis, vero?</p>
                        </div>
                </div>
                <div class="carousel-item">
                    <img src="images/announcement-2.jpg" class="img-fluid">
                        <div class="carousel-caption">
                            <h3>lorem</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Inventore, itaque.</p>
                        </div> 
                </div>
                <div class="carousel-item">
                    <img src="images/announcement-3.jpg" class="img-fluid">
                        <div class="carousel-caption">
                            <h3>lorem</h3>
                            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Molestiae, amet.</p>
                        </div>  
                </div>
                </div>
    
                <!-- Left and right controls/icons -->
                <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                </button>
            </div>


            <div class="about-text">
                <div class="container mt-3">
                    <h2 class="text-light">lorem</h2>
                    <p class="text-light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis quae, molestiae et tempore odio mollitia natus aliquam necessitatibus! Magnam labore adipisci eius tempora commodi animi voluptatem ut, architecto itaque est?</p>
                    <div id="accordion">

                        <div class="card">
                          <div class="card-header">
                            <a class="btn" data-bs-toggle="collapse" href="#collapseOne">
                              What is Eulogio Rodriguez Vocational High School?
                            </a>
                          </div>
                          <div id="collapseOne" class="collapse" data-bs-parent="#accordion">
                            <div class="card-body">
                              Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsum ipsa suscipit neque magnam nulla aspernatur tempora ad, similique exercitationem numquam eos aliquid. Nostrum iste, sapiente ducimus similique saepe dolores repudiandae. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla, voluptate consequatur consequuntur distinctio porro eius vitae optio rerum corrupti earum non exercitationem quos, animi iure totam accusamus quasi, dignissimos fugit?
                            </div>
                          </div>
                        </div>
            
                        <div class="card">
                          <div class="card-header">
                            <a class="collapsed btn" data-bs-toggle="collapse" href="#collapseTwo">
                              What is the purpose of this website?
                          </a>
                          </div>
                          <div id="collapseTwo" class="collapse" data-bs-parent="#accordion">
                            <div class="card-body">
                              Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </div>
                          </div>
                        </div>
            
                        <div class="card">
                          <div class="card-header">
                            <a class="collapsed btn" data-bs-toggle="collapse" href="#collapseThree">
                            Mission and Vision of Eulogio Rodriguez Vocational High School?
                            </a>
                          </div>
                          <div id="collapseThree" class="collapse" data-bs-parent="#accordion">
                            <div class="card-body">
                              Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </div>
                          </div>
                        </div>
            
                       
                        
                      </div>
                </div>
            </div>
  
 
        </div>

       

    </section>

    <section class="contact-section p-5 bg-light" id="contact">
    <div class="container-fluid">
        <div class="display-3 mt-5 text-danger">
            Contact Us
        </div>

        <div class="row mt-4">
            <div class="col-md-3">
                <h5>Address:</h5>
                <p>Nagtahan Street, Barangay 634-635, Manila, Metro Manila</p>
                <h5>Email:</h5>
                <p>info@ervhs.edu.ph</p>
                <h5>Phone:</h5>
                <p>(02) 123-4567</p>
            </div>
            <div class="col-md-5">
                <h5>Location:</h5>
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.989217542529!2d121.0009877!3d14.5991566!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b5a6b5b5b5b5%3A0x5b5b5b5b5b5b5b5b!2sNagtahan%20Street%2C%20Barangay%20634-635%2C%20Manila%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1634567890123!5m2!1sen!2sph&markers=color:red%7Clabel:C%7C14.5991566,121.0009877&icon=https://maps.google.com/mapfiles/kml/shapes/schools_maps.png" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
            <div class="col-md-4">
                <h5>Send us a message:</h5>
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Submit</button>
                </form>
            </div>
        </div>
    </div>
</section>






    



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>