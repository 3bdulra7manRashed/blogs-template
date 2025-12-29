# Google reCAPTCHA v3 Setup Guide

Google reCAPTCHA v3 has been successfully integrated into your contact form. Follow these steps to complete the setup.

## 🔑 Step 1: Get reCAPTCHA Keys

1. Go to [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
2. Click **"+"** to create a new site
3. Fill in the form:
   - **Label**: Your site name (e.g., "My Blog Contact Form")
   - **reCAPTCHA type**: Select **"reCAPTCHA v3"**
   - **Domains**: Add your domains (e.g., `localhost`, `yourdomain.com`)
   - Accept the terms
4. Click **Submit**
5. Copy your **Site Key** and **Secret Key**

## 🔧 Step 2: Configure Environment Variables

Add these lines to your `.env` file:

```env
# Google reCAPTCHA v3
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
RECAPTCHA_THRESHOLD=0.5
```

**Note**: `RECAPTCHA_THRESHOLD` is the minimum score (0.0 to 1.0) required to pass validation. 
- 0.5 is recommended (balanced security)
- 0.7+ for stricter validation
- 0.3 for more lenient validation

Also add these to your `.env.example` file for team members:

```env
# Google reCAPTCHA v3
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=
RECAPTCHA_THRESHOLD=0.5
```

## 📋 Step 3: Test the Integration

1. **Clear cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Visit contact page**: Navigate to `/contact`

3. **Submit the form**: Fill out the form and submit
   - reCAPTCHA v3 works invisibly in the background
   - No checkbox or challenge for users
   - If score is too low, validation will fail

4. **Check for errors**: If validation fails, you'll see an error message in Arabic

## 🛠️ Configuration Details

### Files Modified/Created:

1. **`app/Rules/ReCaptcha.php`** ✅ CREATED
   - Custom validation rule for reCAPTCHA
   - Verifies token with Google's API
   - Checks score against threshold

2. **`config/services.php`** ✅ UPDATED
   - Added reCAPTCHA configuration

3. **`app/Http/Controllers/PageController.php`** ✅ UPDATED
   - Added reCAPTCHA validation to `sendContact()` method

4. **`resources/views/pages/contact.blade.php`** ✅ UPDATED
   - Added reCAPTCHA script
   - Added form submission handler
   - Added error display

## 🔒 How It Works

### reCAPTCHA v3 (Invisible)

1. **User fills form** → No interruption, no checkbox
2. **User clicks submit** → JavaScript intercepts
3. **Google analyzes behavior** → Returns a score (0.0 - 1.0)
4. **Token sent to server** → Backend validates with Google
5. **Score checked** → If score ≥ threshold, form submits
6. **Success/Failure** → User sees result

### Score Interpretation:
- **1.0** → Very likely a human
- **0.5** → Uncertain
- **0.0** → Very likely a bot

## 🧪 Testing

### Test as Human:
1. Navigate naturally to the contact page
2. Fill out the form normally
3. Submit
4. ✅ Should succeed (score will be high)

### Test as Bot (simulated):
```bash
# Send POST request without reCAPTCHA token
curl -X POST http://yoursite.com/contact \
  -d "name=Test" \
  -d "email=test@example.com" \
  -d "message=Test message"
```
❌ Should fail with validation error

## 🚨 Troubleshooting

### Issue: "يرجى إكمال التحقق من reCAPTCHA"
**Solution**: 
- Ensure `RECAPTCHA_SITE_KEY` is set correctly in `.env`
- Clear config cache: `php artisan config:clear`

### Issue: "فشل التحقق من reCAPTCHA"
**Solutions**:
- Check that `RECAPTCHA_SECRET_KEY` is correct
- Ensure your domain is registered in Google reCAPTCHA console
- For localhost, add `localhost` to allowed domains

### Issue: Score too low (legitimate users blocked)
**Solution**: Lower the threshold in `.env`:
```env
RECAPTCHA_THRESHOLD=0.3  # More lenient
```

### Issue: reCAPTCHA not loading
**Solution**:
- Check browser console for JavaScript errors
- Ensure internet connection is active
- Verify reCAPTCHA script loads: View page source and check for Google script

## 🔄 Disable reCAPTCHA (For Testing)

To temporarily disable reCAPTCHA validation:

1. **Option 1**: Remove keys from `.env`
   ```env
   RECAPTCHA_SITE_KEY=
   RECAPTCHA_SECRET_KEY=
   ```

2. **Option 2**: Comment out validation in controller
   ```php
   // 'g-recaptcha-response' => ['required', new \App\Rules\ReCaptcha()],
   ```

3. Clear cache: `php artisan config:clear`

## 📊 Monitoring

To monitor reCAPTCHA scores and bot activity:

1. Visit [Google reCAPTCHA Analytics](https://www.google.com/recaptcha/admin)
2. Select your site
3. View **Analytics** tab
4. Check:
   - Request volume
   - Score distribution
   - Suspicious activity

## 🌐 Production Deployment

Before deploying to production:

1. ✅ Add production domain to Google reCAPTCHA console
2. ✅ Set production keys in `.env`
3. ✅ Test form submission on production
4. ✅ Monitor analytics for first week
5. ✅ Adjust threshold if needed

## 🔐 Security Best Practices

1. **Never commit** `.env` file to version control
2. **Keep secret key private** - Never expose in frontend code
3. **Use HTTPS** in production - reCAPTCHA requires secure connection
4. **Monitor scores** regularly - Adjust threshold based on patterns
5. **Backup plan** - Have rate limiting as fallback protection

## 📚 Additional Resources

- [Google reCAPTCHA v3 Documentation](https://developers.google.com/recaptcha/docs/v3)
- [reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
- [Best Practices Guide](https://developers.google.com/recaptcha/docs/v3#best_practices)

---

## ✅ Quick Checklist

- [ ] Create reCAPTCHA site at Google console
- [ ] Copy Site Key and Secret Key
- [ ] Add keys to `.env` file
- [ ] Add keys to `.env.example` (empty values)
- [ ] Run `php artisan config:clear`
- [ ] Test contact form submission
- [ ] Verify error handling works
- [ ] Add production domain before deployment
- [ ] Monitor analytics after launch

---

**Integration Status**: ✅ Complete

Your contact form is now protected with Google reCAPTCHA v3!

