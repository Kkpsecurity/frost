<div class="footer-content p-3">
  <form method="post" action="{{ url('join_newsletter') }}">
    @csrf
    <div class="input-group mb-3">
      <input type="email" name="email" class="form-control" placeholder="Type Email" required>
      <button type="submit" class="btn btn-primary">Subscribe</button>
    </div>
  </form>
</div>
