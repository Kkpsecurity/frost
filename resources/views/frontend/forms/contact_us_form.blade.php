<form id="contactForm" method="POST" action="{{ route('pages', ['contact', 'send']) }}"
class="contact-form">
@csrf
<div class="col-md-6 col-sm-6 col-12">
    <div class="form-group">
        <input type="text" id="name" name="name" class="form-control"
            placeholder="Name" required data-error="Please enter your name">
        <div class="invalid-feedback">Please enter your name</div>
    </div>
</div>
<div class="col-md-6 col-sm-6 col-12">
    <div class="form-group">
        <input type="email" id="email" name="email" class="email form-control"
            placeholder="Email" required data-error="Please enter your email">
        <div class="invalid-feedback">Please enter a valid email</div>
    </div>
</div>
<div class="col-md-12 col-12">
    <div class="form-group">
        <input type="text" id="msg_subject" name="subject" class="form-control"
            placeholder="Subject" required data-error="Please enter your message subject">
        <div class="invalid-feedback">Please enter the subject of your message</div>
    </div>
</div>
<div class="col-md-12 col-12">
    <div class="form-group">
        <textarea id="message" name="message" rows="7" placeholder="Message" class="form-control" required
            data-error="Write your message"></textarea>
        <div class="invalid-feedback">Please enter your message</div>
    </div>
</div>
<div class="col-md-12 col-12">
    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="privacy_agree" name="privacy_agree" required>
        <label class="form-check-label text-white" for="privacy_agree">
            By submitting this form, you agree to our 
            <a href="{{ route('pages', 'privacy') }}" target="_blank" class="text-info">Privacy Policy</a>
        </label>
        <div class="invalid-feedback">You must agree to the Privacy Policy</div>
    </div>
</div>
<div class="col-md-12 col-12 text-center">
    <button type="submit" id="submit" class="btn btn-primary contact-btn">Send
        Message</button>
    <div id="msgSubmit" class="h3 text-center hidden"></div>
    <div class="clearfix"></div>
</div>
</form>