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

        <div class="container mt-5">
            <div class="card p-5">


                <form id="registration_form" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name">First Name</label>
                            <input
                                type="text"
                                class="form-control"
                                id="first_name"
                                placeholder="First Name"
                                required />
                            <div class="invalid-feedback">
                                Please provide a valid first name.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name">Last Name</label>
                            <input
                                type="text"
                                class="form-control"
                                id="last_name"
                                placeholder="Last Name"
                                required />
                            <div class="invalid-feedback">
                                Please provide a valid last name.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="uusername">Username</label>
                            <input
                                type="text"
                                class="form-control"
                                id="uusername"
                                placeholder="Username"
                                required />
                            <div class="invalid-feedback">Please choose a username.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                placeholder="Email"
                                required />
                            <div class="invalid-feedback">Please provide a valid email.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone_number">Phone Number</label>
                            <input
                                type="tel"
                                class="form-control"
                                id="phone_number"
                                placeholder="Phone Number"
                                required />
                            <div class="invalid-feedback">
                                Please provide a valid phone number.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expertise">Your Expertise</label>
                            <select class="form-control" id="expertise" required>
                                <option selected disabled value="">Choose your expertise</option>
                                <option>Math</option>
                                <option>Science</option>
                                <option>History</option>
                                <option>Computer Science</option>
                                <option>Languages</option>
                            </select>
                            <div class="invalid-feedback">Please select an expertise.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profile_picture">Profile Picture (512*512)</label>
                            <input
                                type="file"
                                class="form-control"
                                id="profile_picture"
                                required />
                            <img
                                id="profile_preview"
                                class="img-thumbnail mt-2"
                                style="display: none" />
                            <div class="invalid-feedback">Please upload a profile picture.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cover_image">Cover Image (600*300)</label>
                            <input type="file" class="form-control" id="cover_image" required />
                            <img
                                id="cover_preview"
                                class="img-thumbnail mt-2"
                                style="display: none" />
                            <div class="invalid-feedback">Please upload a cover image.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ppassword">Password</label>
                            <input
                                type="password"
                                class="form-control"
                                id="ppassword"
                                placeholder="Password"
                                required />
                            <div class="invalid-feedback">Please provide a password.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password">Confirm Password</label>
                            <input
                                type="password"
                                class="form-control"
                                id="confirm_password"
                                placeholder="Confirm Password"
                                required />
                            <div class="invalid-feedback">Please confirm your password.</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value=""
                                id="terms_conditions"
                                required />
                            <label class="form-check-label" for="terms_conditions">
                                Agree to terms and conditions
                            </label>
                            <div class="invalid-feedback">
                                You must agree before submitting.
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3 w-100 button-style" type="submit">Submit form</button>
                </form>
            </div>
        </div>
<?php
    }
}
