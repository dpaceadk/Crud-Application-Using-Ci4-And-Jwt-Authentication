<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<div class="card card-primary rounded-0">
    <div class="card-header">
        <h4 class="text-muted"><i class="far fa-plus-square"></i> Add New Contact Details</h4>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <?php if (session()->has('success_message')): ?>
                <div class="alert alert-success">
                    <?= session()->get('success_message') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->get('error') ?>
                </div>
            <?php endif; ?>

            <form id="create-form">
                <input type="hidden" name="id">
                
                <div class="mb-3">
                    <label for="firstname" class="control-label">Fullname (first name, middle name, last name)</label>
                    <div class="input-group">
                        <input type="text" autofocus class="form-control form-control-border" id="firstname" name="firstname" value="<?= old('firstname') ?>" required placeholder="First Name" autocomplete="off">
                        <input type="text" class="form-control form-control-border" id="middlename" name="middlename" value="<?= old('middlename') ?>" placeholder="Middle Name (optional)" autocomplete="off">
                        <input type="text" class="form-control form-control-border" id="lastname" name="lastname" value="<?= old('lastname') ?>" required placeholder="Last Name" autocomplete="off">
                    </div>
                </div>
                <div class="mb-3 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <label for="gender" class="control-label">Gender</label>
                    <select name="gender" id="gender" class="form-select form-select-border" required>
                        <option value="Male" <?= old('gender') == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= old('gender') == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label for="contact" class="control-label">Contact </label>
                            <input type="text" class="form-control" id="contact" name="contact" required pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" placeholder="Phone Number" value="<?= old('contact') ?>">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label for="email" class="control-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="Email Address" value="<?= old('email') ?>">
                        </div>
                    </div>
                </div>
                <div class="mb-3 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <label for="address" class="control-label">Address</label>
                    <textarea name="address" id="address" cols="30" rows="3" class="form-control" required placeholder="Enter Address"><?= old('address') ?></textarea>
                </div>
                <div class="mb-3">
                    <div class="g-recaptcha" data-sitekey="6LfWkv0pAAAAAN-l-qfDNH6tyGeEqRnZnkx17VG9"></div>
                </div>
                <div class="card-footer text-center">
                    <button type="button" class="btn btn-primary" id="save-button"><i class="fa fa-save"></i> Save Details</button>
                    <button type="reset" class="btn btn-secondary"><i class="fa fa-times"></i> Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include reCAPTCHA API script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
$(document).ready(function() {
    $('#save-button').click(function(event) {
        event.preventDefault();
        var recaptchaResponse = grecaptcha.getResponse();
        if (recaptchaResponse.length === 0) {
            alert("Please complete the reCAPTCHA");
            return;
        }

        $.ajax({
            url: '<?= base_url('main/save') ?>',
            type: 'POST',
            data: $('#create-form').serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Details saved successfully.');
                    window.location.href = '<?= base_url('list') ?>'; // Redirect to list page
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    });
});
</script>
