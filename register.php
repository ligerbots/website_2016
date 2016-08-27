<?php
require_once( "include/page_elements.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require('wp-backend/wp-blog-header.php');
?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Registration", false, '/css/register.css' ); ?>
  
  <body>
    <div id="header-ghost" ></div>
    <div class="container-fluid no-side-padding">
      <div class="col-lg-10 col-lg-offset-1 col-md-12">

        <?php 
        output_header(); 
        output_navbar();
        ?>

        <div class="page-body">
          <div class="row side-margins bottom-margin"> </div>

          <div class="row side-margins bottom-margin">
            <div class="col-lg-6 col-lg-offset-3 col-xs-12 no-side-padding bottom-margin">
              
              <form method="post" action="https://form-echo.azurewebsites.net/" onsubmit="return(validate());">
                <div class="form-group">
                  <label class="required">Name</label>
                  <div class="lb-input-group name-group">
                    <input type="text" class="form-control" placeholder="John" name="first-name" required />
                    <input type="text" class="form-control" placeholder="Smith" name="last-name" required />
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="required">Email</label>
                  <input type="email" class="form-control" placeholder="john.smith@example.com" name="email" required />
                </div>
                
                <div class="form-group phone-group">
                  <label class="required">Phone</label>
                  <input type="text" maxlength="12" class="form-control" placeholder="123-456-7890" name="phone" required />
                </div>
                
                <div class="form-group">
                  <label class="required">Address</label>
                  <input type="text" class="form-control" placeholder="Address line" name="address-line" required />
                  <div class="spacer"></div>
                  <div class="lb-input-group address-group">
                    <input type="text" class="form-control" placeholder="City" value="Newton" name="address-city" required />
                    <input type="text" class="form-control" placeholder="State" value="MA" name="address-state" required />
                    <input type="number" class="form-control" placeholder="Zip code" name="address-zip" maxlength="5" required />
                  </div>
                </div>
                
                <label class="required">School</label>
                <div class="radio lb-checkbox">
                  <label>
                    <input type="radio" name="school" value="north" required /> Newton North
                  </label>
                  <label>
                    <input type="radio" name="school" value="south" required /> Newton South
                  </label>
                  <label>
                    <input type="radio" name="school" value="none" required /> N/A
                  </label>
                </div>
                
                <label class="required">I am a...</label>
                <div class="radio lb-checkbox">
                  <label>
                    <input type="radio" name="user-type" value="student" required /> Student
                  </label>
                  <label>
                    <input type="radio" name="user-type" value="adult" required /> Adult
                  </label>
                </div>
                
                <div class="roles-group">
                  <label>Additional roles</label>
                  <div class="checkbox lb-checkbox">
                    <label class="hidden">
                      <input type="checkbox" name="role-parent" disabled /> Parent/Guardian
                    </label>
                    <label class="hidden">
                      <input type="checkbox" name="role-mentor" disabled /> Mentor
                    </label>
                    <label class="hidden">
                      <input type="checkbox" name="role-coach" disabled /> Coach
                    </label>
                    <label class="hidden">
                      <input type="checkbox" name="role-exec" disabled /> Executive
                    </label>
                  </div>
                </div>
                
                <!-- There are two different sections of the form based on whether the user selected "student" or "parent" -->
                <div class="form-sections">
                  <div class="form-section student">
                    <label class="required">Parent/Guardian names</label>
                    <!-- allows for multiple copies of input elements via some scripting 
                         used to display a variable number of parent name inputs -->
                    <div class="multi-input">
                      <script type="text/html+template" class="multi-input-template">
                        <div class="lb-input-group name-group multi-name-group">
                          <input type="text" class="form-control" placeholder="John" name="parent-first-name[]" data-required />
                          <input type="text" class="form-control" placeholder="Smith" name="parent-last-name[]" data-required />
                          <span class="input-group-btn">
                            <button class="btn btn-default multi-input-remove" type="button">X</button>
                          </span>
                        </div>
                      </script>
                      <div class="multi-input-items"></div>
                      <div class="spacer"></div>
                      <div class="multi-input-buttons">
                        <button type="button" class="btn multi-input-add">
                          Add
                        </button>
                      </div>
                      <div class="spacer"></div>
                    </div>
                    
                    <div class="form-group">
                      <label class="required">Parent/Guardian email</label>
                      <input type="email" class="form-control" placeholder="john.smith@example.com" name="parent-email" data-required />
                    </div>
                    
                    <div class="form-group phone-group">
                      <label class="required">Emergency Phone</label>
                      <input type="text" maxlength="12" class="form-control" placeholder="123-456-7890" name="emergency-phone" required />
                    </div>
                    
                    <div class="form-group">
                      <label>Graduation year</label>
                      <input type="number" class="form-control" name="graduation-year" min="2017" max="2035" />
                    </div>
                  </div>
                  <div class="form-section parent">
                    <label class="required">Child(ren)'s Name(s)</label>
                    <!-- same idea as the one for the student section -->
                    <div class="multi-input">
                      <script type="text/html+template" class="multi-input-template">
                        <div class="lb-input-group name-group multi-name-group">
                          <input type="text" class="form-control" placeholder="John" name="child-first-name[]" data-required />
                          <input type="text" class="form-control" placeholder="Smith" name="child-last-name[]" data-required />
                          <span class="input-group-btn">
                            <button class="btn btn-default multi-input-remove" type="button">X</button>
                          </span>
                        </div>
                      </script>
                      <div class="multi-input-items"></div>
                      <div class="spacer"></div>
                      <div class="multi-input-buttons">
                        <button type="button" class="btn multi-input-add">
                          Add
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="spacer"></div>

                <div class="form-group">
                  <label class="required">Password</label>
                  <div class="lb-input-group">
                    <input type="password" pattern=".{8,}" class="form-control" placeholder="Password" name="password" aria-describedby="passwordHelp" required />
                    <input type="password" class="form-control" placeholder="Confirm" name="password-confirm" required />
                  </div>
                  <span id="passwordHelp" class="help-block">Password must be a minimum of 8 characters and contain mixed case and at least 1 digit.</span>
                </div>
                
                <div class="help-block">
                  <label class="required"></label> required
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
              </form>
            </div>
          </div>

          <?php output_footer(); ?>
          
        </div>
      </div>
    </div>

    <?php page_foot(); ?>
    <script src="/include/register.js"></script>
  </body>
</html>
