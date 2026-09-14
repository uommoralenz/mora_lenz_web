@php
    use App\Http\Controllers\Public_\ContactController;

    $social = config('moralenz.social');
    $email = config('moralenz.contact_email');
@endphp

<section id="contact" class="section contact">
    <div class="container">
        <div class="section__head">
            <h2 class="section__title">Get In Touch</h2>
            <p class="section__lede">
                Have questions about our club? Want to collaborate on a project?
                We&rsquo;d love to hear from you!
            </p>
        </div>

        <div class="contact__grid">
            {{-- Form --}}
            <div>
                <h3 class="contact__subtitle">Send us a Message</h3>

                @if (session('contact_success'))
                    <div class="alert alert--success" data-contact-alert role="status">
                        {{ session('contact_success') }}
                    </div>
                @endif

                @if (session('contact_error'))
                    <div class="alert alert--error" data-contact-alert role="alert">
                        {{ session('contact_error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert--error" data-contact-alert role="alert">
                        Please check the highlighted fields and try again.
                    </div>
                @endif

                <form class="form" method="POST" action="{{ \App\Support\Links::contactPost() }}" data-contact-form novalidate>
                    @csrf

                    {{-- Honeypot: left empty by people, filled in by bots. --}}
                    <div class="form__hp" aria-hidden="true">
                        <label for="website">Leave this field empty</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form__row">
                        <div class="form__field">
                            <label for="contact-name">Name *</label>
                            <input type="text" id="contact-name" name="name" placeholder="Your name"
                                   value="{{ old('name') }}" maxlength="120" required>
                            @error('name') <p class="form__error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form__field">
                            <label for="contact-email">Email *</label>
                            <input type="email" id="contact-email" name="email" placeholder="your.email@example.com"
                                   value="{{ old('email') }}" maxlength="180" required>
                            @error('email') <p class="form__error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="form__field">
                        <label for="contact-subject">Subject *</label>
                        <select id="contact-subject" name="subject" required>
                            <option value="" disabled @selected(! old('subject'))>Select a subject</option>
                            @foreach (ContactController::SUBJECTS as $value => $label)
                                <option value="{{ $value }}" @selected(old('subject') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('subject') <p class="form__error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form__field">
                        <label for="contact-message">Message *</label>
                        <textarea id="contact-message" name="message" rows="6"
                                  placeholder="Tell us about your inquiry..."
                                  maxlength="5000" required>{{ old('message') }}</textarea>
                        @error('message') <p class="form__error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="form__submit">Send Message</button>
                </form>
            </div>

            {{-- Info --}}
            <div class="contact__info">
                <div>
                    <h3 class="contact__subtitle">Contact Information</h3>

                    <div class="contact__item">
                        <div class="contact__icon contact__icon--mail">
                            @include('partials.icons', ['icon' => 'mail'])
                        </div>
                        <div>
                            <h3>Email</h3>
                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                            <p>Send us an email anytime</p>
                        </div>
                    </div>

                    <div class="contact__item">
                        <div class="contact__icon contact__icon--map">
                            @include('partials.icons', ['icon' => 'map-pin'])
                        </div>
                        <div>
                            <h3>Location</h3>
                            <a href="{{ config('moralenz.map_url') }}" target="_blank" rel="noopener noreferrer">
                                University of Moratuwa
                            </a>
                            <p>Katubedda, Moratuwa, Sri Lanka</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="contact__subtitle">Follow Us</h3>
                    <div class="socials">
                        <a href="{{ $social['facebook'] }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.45 2.89h-2.33v6.99A10 10 0 0 0 22 12Z"/>
                            </svg>
                        </a>
                        <a href="{{ $social['instagram'] }}" target="_blank" rel="noopener noreferrer"
                           class="is-instagram" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </a>
                        <a href="{{ $social['linkedin'] }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3 9h4v12H3V9Zm7 0h3.8v1.71h.05c.53-.95 1.83-1.96 3.77-1.96C21.4 8.75 22 11.1 22 14.14V21h-4v-6.07c0-1.45-.03-3.31-2.02-3.31-2.02 0-2.33 1.57-2.33 3.2V21h-3.98V9Z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="contact__subtitle">Find Us</h3>
                    <div class="map-embed">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.2851474219845!2d79.89739731477453!3d6.8509937950764985!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae25a5c0fc3a0b9%3A0x1d3c3f0dc88f0f0f!2sUniversity%20of%20Moratuwa!5e0!3m2!1sen!2slk!4v1644567890123!5m2!1sen!2slk"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="University of Moratuwa location"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
