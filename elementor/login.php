<?php
class Elementor_fiverr_market_login_form extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'teach-login';
    }

    public function get_title()
    {
        return esc_html__('Login', 'fiverr-market');
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
        return ['login', 'form'];
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
            <div class="card lform p-5">
                <div id="login-error-message" class="alert alert-danger" style="display:none;"></div>

                <form id="login-form" class="needs-validation" novalidate>
                    <div class="form-group mb-3">
                        <label for="username">Username or Email</label>
                        <input type="text" id="lusername" class="form-control" placeholder="Enter username or email">
                        <div class="invalid-feedback">Please enter your username or email.</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input type="password" id="lpassword" class="form-control" placeholder="Enter password">
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3 w-100 button-style">Login</button>
                </form>
            </div>
        </div>
<?php
    }
}
