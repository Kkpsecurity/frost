<div class="form-group">
  <label for="cardNumber">Card Number</label>
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text" id="card-number-addon">
        <i class="fab fa-cc-visa"></i>
      </span>
    </div>
    <input type="text" class="form-control" id="cardNumber" name="cardNumber" placeholder="Enter card number" data-paypal-button autocomplete="cc-number">
  </div>
</div>
<div class="form-group">
  <label for="expirationDate">Expiration Date</label>
  <input type="text" class="form-control" id="expirationDate" name="expirationDate" placeholder="MM/YY" autocomplete="cc-exp">
</div>
<div class="form-group">
  <label for="cvv">CVV</label>
  <input type="text" class="form-control" id="cvv" name="cvv" placeholder="Enter CVV" autocomplete="cc-csc">
</div>

<div class="form-group form-check">
    <input type="checkbox" class="form-check-input" id="privacy_agree" name="privacy_agree" required>
    <label class="form-check-label" for="privacy_agree">
        By submitting this form, you agree to our 
        <a href="{{ route('pages', 'privacy') }}" target="_blank" class="text-info">Privacy Policy</a>
    </label>
</div>
