<?php
class Elementor_fiverr_market_student_registration_form extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'student-regi';
    }

    public function get_title()
    {
        return esc_html__('ST R Form', 'fiverr-market');
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
        return ['student', 'form'];
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
                <form id="sregistration_form" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName">First Name</label>
                            <input type="text" class="form-control" id="firstName" placeholder="Enter first name" required>
                            <div class="invalid-feedback">Please provide a valid first name.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName">Last Name</label>
                            <input type="text" class="form-control" id="lastName" placeholder="Enter last name" required>
                            <div class="invalid-feedback">Please provide a valid last name.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username">User Name</label>
                            <input type="text" class="form-control" id="username" placeholder="Enter username" required>
                            <div class="invalid-feedback">Please provide a valid username.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter email address" required>
                            <div class="invalid-feedback">Please provide a valid email address.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" placeholder="Enter phone number" required>
                            <div class="invalid-feedback">Please provide a valid phone number.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" required>
                                <option value="" disabled selected>Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="invalid-feedback">Please select a valid gender.</div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="age">Age</label>
                            <input type="number" class="form-control" id="age" min="0" placeholder="Enter age" required>
                            <div class="invalid-feedback">Please provide a valid age.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div id="parent-email-group" style="display: none;">
                                <label for="parentEmail">Parent Email</label>
                                <input type="email" class="form-control" id="parentEmail" placeholder="Enter parent email">
                                <div class="invalid-feedback">Please provide a valid parent email.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Enter password" required>
                            <div class="invalid-feedback">Please provide a valid password.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm password" required>
                            <div class="invalid-feedback">Passwords do not match.</div>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3 w-100 button-style" type="submit">Resgister Student</button>
                </form>


            </div>
        </div>
<?php
    }
}
?>