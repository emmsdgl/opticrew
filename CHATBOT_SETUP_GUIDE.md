# Fin-noys Chatbot Setup Guide

**Status**: ✅ **Integrated and Ready to Configure**
**Date**: October 24, 2025

---

## 🎯 Overview

Your homepage now has an **AI-powered chatbot** specifically trained to answer questions about **Fin-noys Cleaning Services**. The chatbot uses Google's Gemini AI and is configured to only respond to questions related to your cleaning business.

---

## ✨ Features

### ✅ Company-Specific Training
- **Knowledge Base**: Trained on comprehensive Fin-noys company information
- **Services**: Hotel cleaning, daily cleaning, snow removal
- **Booking Info**: Guides users to book online through your website
- **Professional Tone**: Friendly, helpful, and on-brand responses

### ✅ Smart Restrictions
- **Only answers** cleaning and Fin-noys related questions
- **Redirects off-topic** questions back to company services
- **Encourages booking** by directing users to "Get Started" button
- **Provides quotes** by directing to "Get a Free Quote" feature

### ✅ Security & Performance
- **Backend API**: Secure server-side processing
- **Rate Limited**: 30 requests per minute to prevent abuse
- **Error Handling**: Graceful fallbacks for API issues
- **Retry Logic**: Automatic retry with exponential backoff

---

## 🚀 Setup Instructions

### Step 1: Get Your Gemini API Key

1. **Visit Google AI Studio**:
   ```
   https://aistudio.google.com/app/apikey
   ```

2. **Sign in** with your Google account

3. **Click "Create API Key"**

4. **Copy the API key** (it will look like: `AIzaSyA...`)

---

### Step 2: Add API Key to Your Project

1. **Open your `.env` file**:
   ```
   C:\xampp\htdocs\opticrew\.env
   ```

2. **Add this line** at the end:
   ```env
   GEMINI_API_KEY=your_actual_api_key_here
   ```

3. **Replace** `your_actual_api_key_here` with the key you copied

4. **Save the file**

**Example**:
```env
GEMINI_API_KEY=AIzaSyBdKqX1234567890abcdefghijklmnop
```

---

### Step 3: Clear Cache (Important!)

Run this command to refresh the configuration:

```bash
cd C:\xampp\htdocs\opticrew
php artisan config:clear
php artisan cache:clear
```

---

### Step 4: Test the Chatbot

1. **Open your homepage**:
   ```
   http://localhost
   ```

2. **Click the blue chat button** (bottom right corner)

3. **Test with these questions**:
   - "What services does Fin-noys offer?"
   - "How do I book a hotel cleaning service?"
   - "Do you provide snow removal?"
   - "How much does cleaning cost?"
   - "What makes Fin-noys different?"

4. **Try an off-topic question**:
   - "What's the weather today?"
   - Expected: Chatbot should redirect to cleaning services

---

## 📁 Files Created/Modified

### New Files (3):

1. **`storage/app/finnoys_knowledge_base.txt`**
   - **Purpose**: Comprehensive company information database
   - **Contains**: Services, pricing, booking process, FAQs, company values
   - **Editable**: YES - Update this file to change chatbot knowledge

2. **`app/Http/Controllers/ChatbotController.php`**
   - **Purpose**: Backend API handler for chatbot requests
   - **Features**: AI integration, error handling, rate limiting
   - **Security**: API key stored in environment, not exposed to frontend

3. **`CHATBOT_SETUP_GUIDE.md`** (this file)
   - Complete setup and usage instructions

### Modified Files (3):

4. **`routes/api.php`**
   - **Added**: `POST /api/chatbot/message` endpoint
   - **Security**: Public endpoint with rate limiting (30 req/min)

5. **`.env.example`**
   - **Added**: `GEMINI_API_KEY` placeholder
   - **Purpose**: Template for setting up API key

6. **`resources/views/html/landing-page/home.html`**
   - **Modified**: Chatbot JavaScript to use Laravel backend
   - **Changed**: Removed hardcoded API key (security improvement)
   - **Improved**: Better error handling and response processing

---

## 🔒 Security Features

### ✅ API Key Protection
- **Never exposed** to frontend/browser
- **Stored securely** in `.env` file (not in git)
- **Server-side only**: All AI requests go through Laravel backend

### ✅ Rate Limiting
- **30 requests per minute** per IP address
- **Prevents abuse** and excessive API costs
- **Automatic throttling** with error messages

### ✅ Input Validation
- **Max 1000 characters** per message
- **Sanitized inputs** to prevent injection attacks
- **Type checking** on all parameters

### ✅ Error Handling
- **Graceful failures**: User-friendly error messages
- **Logging**: All errors logged for debugging
- **Retry logic**: Automatic retries for temporary failures

---

## 💰 Cost Considerations

### Gemini API Pricing (as of 2024)
- **Free Tier**: 15 requests per minute
- **Standard**: Pay-as-you-go (very affordable)
- **Gemini 1.5 Flash**: ~$0.075 per 1M characters

### Estimated Costs
With 30 requests/minute limit:
- **Low traffic**: $0-5/month
- **Medium traffic**: $10-30/month
- **High traffic**: Consider upgrading to paid tier

### Cost Optimization Tips
1. **Monitor usage** via Google AI Studio dashboard
2. **Adjust rate limit** if needed (in `routes/api.php`)
3. **Use free tier** for testing and low traffic

---

## 🎨 Customization Options

### Customize Chatbot Knowledge

**Edit**: `storage/app/finnoys_knowledge_base.txt`

**Update these sections**:
- Company description
- Services offered
- Pricing information
- FAQs
- Contact details

**After editing**, the chatbot will automatically use the new information!

### Customize System Prompt

**Edit**: `app/Http/Controllers/ChatbotController.php` (line 105)

**Modify the `createSystemPrompt()` method** to change:
- Chatbot personality
- Response style (formal vs casual)
- Call-to-action messages
- Tone and language

### Customize Rate Limiting

**Edit**: `routes/api.php` (line 86)

**Change**:
```php
->middleware('throttle:30,1')  // 30 requests per minute
```

**To** (for example, 60 requests):
```php
->middleware('throttle:60,1')  // 60 requests per minute
```

### Customize UI/Styling

**Edit**: `resources/views/html/landing-page/home.html`

**Modify**:
- Chat window colors (line 322-360)
- Button styling (line 355-359)
- Message bubble appearance
- Fonts and text sizes

---

## 🧪 Testing Scenarios

### Test 1: Company Information
**Ask**: "Tell me about Fin-noys"
**Expected**: Overview of company, services, and expertise

### Test 2: Service Details
**Ask**: "What hotel cleaning services do you offer?"
**Expected**: Detailed list of hotel cleaning services

### Test 3: Booking Process
**Ask**: "How do I book a cleaning service?"
**Expected**: Step-by-step booking instructions, encouragement to click "Get Started"

### Test 4: Pricing
**Ask**: "How much does it cost?"
**Expected**: Information about free quotes, directing to "Get a Free Quote"

### Test 5: Off-Topic Redirection
**Ask**: "What's the weather like?"
**Expected**: Polite redirection back to cleaning services

### Test 6: Multi-Turn Conversation
**Try**: Multiple related questions in a row
**Expected**: Context maintained, relevant follow-up answers

---

## 🐛 Troubleshooting

### Issue: "Chatbot service is not configured"

**Cause**: `GEMINI_API_KEY` not set in `.env`

**Fix**:
1. Add `GEMINI_API_KEY=your_key_here` to `.env`
2. Run `php artisan config:clear`

---

### Issue: "The assistant is temporarily unavailable"

**Cause**: API key invalid or quota exceeded

**Fix**:
1. Check API key is correct in `.env`
2. Visit Google AI Studio to check quota
3. Wait a few minutes if rate limited

---

### Issue: Chatbot gives generic answers

**Cause**: Knowledge base not loaded properly

**Fix**:
1. Verify `storage/app/finnoys_knowledge_base.txt` exists
2. Check file permissions (readable by web server)
3. Clear cache: `php artisan cache:clear`

---

### Issue: "Network error occurred"

**Cause**: Internet connection issue or API endpoint down

**Fix**:
1. Check internet connection
2. Verify Laravel server is running
3. Check browser console for errors (F12)

---

### Issue: Chatbot window not opening

**Cause**: JavaScript error or missing elements

**Fix**:
1. Open browser console (F12) and check for errors
2. Verify `home.html` was updated correctly
3. Clear browser cache (Ctrl+Shift+R)

---

## 📊 Monitoring & Analytics

### View API Usage

**Google AI Studio Dashboard**:
```
https://aistudio.google.com/app/apikey
```

**Monitor**:
- Number of requests
- Token usage
- Quota remaining
- Cost estimates

### Laravel Logs

**Location**: `storage/logs/laravel.log`

**Check for**:
- API errors
- Failed requests
- User messages causing errors

**Example**:
```bash
tail -f storage/logs/laravel.log
```

---

## 🔄 Updating Company Information

### When to Update Knowledge Base

Update `storage/app/finnoys_knowledge_base.txt` when:
- ✅ Adding new services
- ✅ Changing pricing
- ✅ Updating contact information
- ✅ Adding new FAQs
- ✅ Modifying company details

### How to Update

1. **Edit** `storage/app/finnoys_knowledge_base.txt`
2. **Save changes**
3. **No restart needed** - Changes apply immediately!

**Note**: The chatbot automatically reads the file on each request.

---

## 🎯 Best Practices

### 1. Keep Knowledge Base Updated
- Review and update quarterly
- Add new FAQs based on common questions
- Remove outdated information

### 2. Monitor Chatbot Performance
- Check logs weekly for errors
- Review user queries to identify gaps
- Update responses based on user needs

### 3. Test After Changes
- Test chatbot after updating knowledge base
- Verify key questions still get good answers
- Check that redirections work properly

### 4. Secure Your API Key
- **Never commit** `.env` to git
- **Don't share** API key publicly
- **Rotate keys** if compromised

### 5. Optimize Costs
- Monitor API usage regularly
- Adjust rate limits based on traffic
- Consider caching for common questions (future enhancement)

---

## 🚀 Next Steps (Optional Enhancements)

### Future Improvements You Could Add:

1. **Conversation History Storage**
   - Save chat logs to database
   - Analyze common questions
   - Improve knowledge base based on queries

2. **Multi-Language Support**
   - Add Finnish language support
   - Detect user language
   - Respond in user's preferred language

3. **Lead Capture**
   - Collect email before providing quote
   - Send follow-up emails
   - Integration with CRM

4. **Analytics Dashboard**
   - Track chatbot usage stats
   - Most asked questions
   - Conversion rate (chat → booking)

5. **Quick Reply Buttons**
   - Pre-defined question buttons
   - Faster user interaction
   - Guide conversation flow

---

## ✅ Checklist

- [ ] Got Gemini API key from Google AI Studio
- [ ] Added `GEMINI_API_KEY` to `.env` file
- [ ] Cleared Laravel cache (`php artisan config:clear`)
- [ ] Tested chatbot on homepage
- [ ] Verified company-specific responses
- [ ] Tested off-topic question redirection
- [ ] Checked error handling
- [ ] Reviewed knowledge base content
- [ ] Set up monitoring (optional)

---

## 📞 Support

### If You Need Help

1. **Check this guide** thoroughly
2. **Review Laravel logs**: `storage/logs/laravel.log`
3. **Check browser console** (F12) for JavaScript errors
4. **Verify API key** is correct and active

### Common Resources

- **Google AI Studio**: https://aistudio.google.com/
- **Gemini API Docs**: https://ai.google.dev/docs
- **Laravel Docs**: https://laravel.com/docs

---

## 🎉 Success!

Your Fin-noys chatbot is now fully integrated and ready to help customers learn about your cleaning services!

**What the chatbot can do**:
✅ Answer questions about Fin-noys services
✅ Provide booking information
✅ Guide users to get quotes
✅ Maintain professional, on-brand conversations
✅ Redirect off-topic questions
✅ Remember conversation context

**Remember**: Add your `GEMINI_API_KEY` to `.env` to activate the chatbot!

---

**Happy Chatting! 🤖✨**
