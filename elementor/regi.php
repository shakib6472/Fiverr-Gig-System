<?php
class Elementor_fiverr_market_teacher_registration_form extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'teacher-regi';
    }

    public function get_title()
    {
        return esc_html__('Teaceher R Form', 'fiverr-market');
    }

    public function get_icon()
    {
        return 'fab fa-teamspeak';
    }

    public function get_categories()
    {
        return ['basic'];
    }

    public function get_keywords()
    {
        return ['teacher', 'form'];
    }

    protected function render()
    {
?>
        <div class="pre-loader">
            <div class="lds-ellipsis">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <div class="error-popup">
                <div class="popup-content">
                    <div class="card">
                        <img src="https://t3.ftcdn.net/jpg/04/21/60/12/360_F_421601274_EUYaZ0sOUgmTdWXBm3MbTRKXFczQpk3u.jpg" alt="">
                        <div class="text text-danger">Username is already Exist</div>
                    </div>
                </div>
        </div>
        <div class="error-success">
                <div class="popup-content">
                    <div class="card">
                        <img src="https://img.freepik.com/premium-vector/green-arrow-pointing-right_1294168-666.jpg" alt="">
                        <div class="text text-success">Thanks For your Application. We will send you an email When it will aproved</div>
                    </div>
                </div>
        </div>

        <div class="container mt-5">
            <div class="card p-5">


                <form id="registration_form" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" placeholder="First Name" required />
                            <div class="invalid-feedback">Please provide a valid first name.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" placeholder="Last Name" required />
                            <div class="invalid-feedback">Please provide a valid last name.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="uusername">Username</label>
                            <input type="text" class="form-control" id="uusername" placeholder="Username" required />
                            <div class="invalid-feedback">Please choose a username.</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Email" required />
                            <div class="invalid-feedback">Please provide a valid email.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" placeholder="Phone Number" required />
                            <div class="invalid-feedback">Please provide a valid phone number.</div>
                        </div>

                        <!-- Dynamic Expertise Dropdown -->
                        <div class="col-md-6 mb-3">
                            <label for="expertise">Your Expertise</label>
                            <select class="form-control" id="expertise" required>
                                <option selected disabled value="">Choose your expertise</option>
                                <?php
                                $expertise_terms = get_terms(array('taxonomy' => 'expertise', 'hide_empty' => false));
                                foreach ($expertise_terms as $term) {
                                    echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Please select an expertise.</div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Dynamic Grade Dropdown -->
                        <div class="col-md-6 mb-3">
                            <label for="grade">Grade</label>
                            <select class="form-control" id="grade" required>
                                <option selected disabled value="">Choose your grade</option>
                                <?php
                                $grade_terms = get_terms(array('taxonomy' => 'grade', 'hide_empty' => false));
                                foreach ($grade_terms as $term) {
                                    echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Please select a grade.</div>
                        </div>

                        <!-- Dynamic Region Dropdown -->
                        <div class="col-md-6 mb-3">
                            <label for="region">Region</label>
                            <select class="form-control" id="region" required>
                                <option selected disabled value="">Choose your region</option>
                                <?php
                                $region_terms = get_terms(array('taxonomy' => 'region', 'hide_empty' => false));
                                foreach ($region_terms as $term) {
                                    echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Please select a region.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profile_picture">Profile Picture (512*512)</label>
                            <input type="file" class="form-control" id="profile_picture" required />
                            <img id="profile_preview" class="img-thumbnail mt-2" style="display: none" />
                            <div class="invalid-feedback">Please upload a profile picture.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cover_image">Cover Image (600*300)</label>
                            <input type="file" class="form-control" id="cover_image" required />
                            <img id="cover_preview" class="img-thumbnail mt-2" style="display: none" />
                            <div class="invalid-feedback">Please upload a cover image.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ppassword">Password</label>
                            <input type="password" class="form-control" id="ppassword" placeholder="Password" required />
                            <div class="invalid-feedback">Please provide a password.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" placeholder="Confirm Password" required />
                            <div class="invalid-feedback">Please confirm your password.</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="terms_conditions" required />
                            <label class="form-check-label" for="terms_conditions">Agree to terms and conditions</label>
                            <div class="invalid-feedback">You must agree before submitting.</div>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3 w-100 button-style" type="submit">Resgister Teacher</button>
                </form>

            </div>
        </div>
<?php
    }
}
