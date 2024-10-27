<div id="plansModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="plans-container">
            <div class="plan1">
                <h2>PHP 1.00 per month</h2>
                <text>1 Peso Free Trial</text>
                <ul>

                    <li>5 Patients</li>
                    <li>5 Printable Progress Reports</li>
                    <li>1 Month Free Access</li>
                </ul>
                <button class="get-plan-btn" onclick="redirectToSignup()">Get This Plan</button>
            </div>
            <div class="plan2">
                <h2>PHP 499.00 per Month</h2>
                <text>Standard Plan</text>
                <ul>
                    <li>20 Patients</li>
                    <li>Trending Graph Data Per Patient</li>
                    <li>10 Printable Progress Reports</li>
                    <li>10 Printable Progress Reports</li>
                </ul>
                <button class="get-plan-btn" onclick="redirectToSignup()">Get This Plan</button>
            </div>
            <div class="plan3">
                <h2>PHP 1299.00 per Month</h2>
                <text>Premium Plan</text>
                <ul>
                    <li>Unlimited Patients</li>
                    <li>Trending Graph Data Per Patient</li>
                    <li>Unlimited Printable Progress Reports</li>
                    <li>10 years access to data progress reports</li>
                    <li>Monthly statistical report of SLP Activity</li>

                </ul>
                <button class="get-plan-btn" onclick="redirectToSignup()">Get This Plan</button>
            </div>
        </div>
    </div>
</div>

<script>
    function redirectToSignup() {
        window.location.href = "signup.php";
    }
</script>