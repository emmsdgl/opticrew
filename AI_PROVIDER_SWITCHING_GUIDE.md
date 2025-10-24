# AI Provider Switching Guide - Gemini ↔ Claude

**Current Setup**: Dual AI Provider Support
**Testing Provider**: Gemini (Free)
**Production Provider**: Claude (Paid, Better Quality)

---

## 🎯 Quick Summary

Your chatbot now supports **BOTH** Gemini and Claude with easy switching:

```
Testing (Now):     Gemini → Free, good quality
Production (Later): Claude → Paid, superior quality
```

**Switch between them** by changing ONE line in `.env`!

---

## 📍 Current Configuration

Your `.env` is currently set to:

```env
AI_PROVIDER=gemini    # Using Gemini for testing
GEMINI_API_KEY=       # Add your Gemini key here
# CLAUDE_API_KEY=...  # Commented out, ready for production
```

---

## 🚀 Step 1: Set Up Gemini (Testing - Now)

### **Get Gemini API Key** (FREE!)

1. **Visit**: https://aistudio.google.com/app/apikey
2. **Sign in** with your Google account
3. **Click "Create API Key"**
4. **Copy the key** (looks like: `AIzaSyA...`)

### **Add to .env**

Open `C:\xampp\htdocs\opticrew\.env` and update:

```env
GEMINI_API_KEY=AIzaSyBdKqX1234567890abcdefghijklmnop
```

### **Clear Cache**

```bash
cd C:\xampp\htdocs\opticrew
php artisan config:clear
```

### **Test It!**

1. Open: `http://localhost`
2. Click chat button
3. Ask: "What is Fin-noys?"
4. Should get response from Gemini!

---

## 🔄 Step 2: Switch to Claude (Production - Later)

When you're ready to go production and have added Claude API credits:

### **Option A: Edit .env (Recommended)**

Open `C:\xampp\htdocs\opticrew\.env`:

```env
# Change this line:
AI_PROVIDER=claude    # Changed from 'gemini' to 'claude'

# Keep Gemini key (for rollback if needed)
GEMINI_API_KEY=AIzaSyBdKqX1234567890abcdefghijklmnop

# Uncomment and add your Claude key:
CLAUDE_API_KEY=your_claude_api_key_here
```

### **Option B: Using Commands**

```bash
cd C:\xampp\htdocs\opticrew

# Switch to Claude
php artisan config:clear

# Test
# (Chatbot will now use Claude automatically)
```

---

## 📊 Comparison: Gemini vs Claude

### **Gemini (Current - Testing)**

| Feature | Rating | Notes |
|---------|--------|-------|
| **Cost** | ⭐⭐⭐⭐⭐ | FREE (15 req/min) |
| **Quality** | ⭐⭐⭐⭐ | Good for testing |
| **On-Topic** | ⭐⭐⭐ | Sometimes wanders |
| **Professional** | ⭐⭐⭐⭐ | Good tone |
| **Best For** | Testing, development |

**Pros**:
- ✅ Completely free
- ✅ Good quality responses
- ✅ Fast responses
- ✅ 15 requests/min free

**Cons**:
- ❌ Can be persuaded off-topic
- ❌ Less consistent personality
- ❌ Not as professional for customer-facing

---

### **Claude (Future - Production)**

| Feature | Rating | Notes |
|---------|--------|-------|
| **Cost** | ⭐⭐⭐ | ~$0.01 per conversation |
| **Quality** | ⭐⭐⭐⭐⭐ | Excellent |
| **On-Topic** | ⭐⭐⭐⭐⭐ | Very strict boundaries |
| **Professional** | ⭐⭐⭐⭐⭐ | Perfect for business |
| **Best For** | Production, customer-facing |

**Pros**:
- ✅ Superior quality
- ✅ Stays strictly on topic
- ✅ Professional, consistent tone
- ✅ Better context understanding
- ✅ Sales-focused conversations

**Cons**:
- ❌ Requires API credits (~$5-10 to start)
- ❌ Separate from Claude Max subscription

---

## 💰 Cost Analysis

### **Gemini (Free Tier)**
- **Cost**: $0
- **Limit**: 15 requests per minute
- **Sufficient for**: Low to medium traffic testing

### **Claude (Production)**
- **Cost per conversation** (5-10 messages): ~$0.01-0.02
- **Monthly estimates**:
  - 100 conversations/day: ~$30-50/month
  - 500 conversations/day: ~$150-250/month
  - 1000 conversations/day: ~$300-500/month

**Initial credits**: Start with $5-10 (250-500 conversations)

---

## 🧪 Testing Both Providers

### **Test with Gemini** (Current Setup)

1. Make sure `.env` has:
   ```env
   AI_PROVIDER=gemini
   GEMINI_API_KEY=your_key_here
   ```

2. Clear cache:
   ```bash
   php artisan config:clear
   ```

3. **Test queries**:
   - "What services does Fin-noys offer?"
   - "How do I book?"
   - "Tell me a joke" ← Should redirect

4. **Observe**:
   - Response speed
   - Answer quality
   - How well it stays on topic

---

### **Test with Claude** (When Ready)

1. Change `.env`:
   ```env
   AI_PROVIDER=claude
   CLAUDE_API_KEY=your_key_here
   ```

2. Clear cache:
   ```bash
   php artisan config:clear
   ```

3. **Test same queries**:
   - Same questions as Gemini
   - Compare response quality
   - Notice stricter topic boundaries

4. **Compare**:
   - Claude should be more professional
   - Better at refusing off-topic
   - More natural conversational flow

---

## 🔧 Technical Details

### **How Switching Works**

The system checks `AI_PROVIDER` in `.env`:

```php
// In ChatbotController.php
$provider = env('AI_PROVIDER', 'gemini'); // Default to Gemini

if ($provider === 'claude') {
    return $this->handleClaudeRequest(...); // Production quality
} else {
    return $this->handleGeminiRequest(...);  // Testing quality
}
```

### **Both Code Paths Exist**

- ✅ **Claude code**: Fully implemented, commented, ready
- ✅ **Gemini code**: Active for testing
- ✅ **Switch**: Just change one environment variable
- ✅ **Rollback**: Easy to switch back if needed

### **Chat History**

Both providers use the same knowledge base and system prompt, so the chatbot personality is consistent regardless of which AI you use.

---

## 📋 Switching Checklist

### **When Testing (Gemini)**

- [ ] Set `AI_PROVIDER=gemini` in `.env`
- [ ] Add Gemini API key from https://aistudio.google.com/app/apikey
- [ ] Clear cache: `php artisan config:clear`
- [ ] Test chatbot on homepage
- [ ] Verify responses are Fin-noys-specific
- [ ] Test off-topic redirection

### **When Going Production (Claude)**

- [ ] Add Claude API credits at https://console.anthropic.com/settings/billing
- [ ] Uncomment `CLAUDE_API_KEY` in `.env`
- [ ] Change `AI_PROVIDER=claude` in `.env`
- [ ] Clear cache: `php artisan config:clear`
- [ ] Test chatbot thoroughly
- [ ] Compare quality vs Gemini
- [ ] Set up usage monitoring
- [ ] Set budget alerts

---

## 🚨 Troubleshooting

### **Issue: "Gemini API is not configured"**

**Cause**: `GEMINI_API_KEY` is empty or invalid

**Fix**:
1. Get key from https://aistudio.google.com/app/apikey
2. Add to `.env`: `GEMINI_API_KEY=AIzaSy...`
3. Run: `php artisan config:clear`

---

### **Issue: "Claude API is not configured"**

**Cause**: Switched to Claude but no API key or credits

**Fix Option 1** (Switch back to Gemini):
```env
AI_PROVIDER=gemini
```

**Fix Option 2** (Add Claude credits):
1. Visit https://console.anthropic.com/settings/billing
2. Purchase API credits ($5-10 minimum)
3. Uncomment `CLAUDE_API_KEY` in `.env`
4. Run: `php artisan config:clear`

---

### **Issue: Still getting Gemini/Claude after switching**

**Cause**: Cache not cleared

**Fix**:
```bash
cd C:\xampp\htdocs\opticrew
php artisan config:clear
php artisan cache:clear
```

---

### **Issue: "The assistant is temporarily unavailable"**

**For Gemini**:
- Check API key is correct
- Verify quota not exceeded (15 req/min free tier)
- Check Google AI Studio console

**For Claude**:
- Check API credits balance
- Verify API key is active
- Check Anthropic console

---

## 💡 Recommendations

### **For Your Development Workflow**

1. **Phase 1: Testing with Gemini** (Current)
   - ✅ Test all chatbot features
   - ✅ Verify knowledge base works
   - ✅ Check off-topic redirection
   - ✅ Get user feedback on responses
   - ✅ Cost: $0

2. **Phase 2: Pre-Production Comparison**
   - Add Claude credits ($5-10)
   - Switch to Claude
   - Test same queries
   - Compare quality side-by-side
   - Decision point: Worth the cost?

3. **Phase 3: Production with Claude**
   - Switch permanently to Claude
   - Monitor usage and costs
   - Adjust rate limiting if needed
   - Keep Gemini as backup

---

## 🎯 Best Practice Strategy

### **Use Gemini For**:
- ✅ Development testing
- ✅ Feature development
- ✅ Learning the system
- ✅ Low-budget projects
- ✅ Internal testing

### **Use Claude For**:
- ✅ Customer-facing chatbot
- ✅ Production website
- ✅ Professional image
- ✅ Better sales conversion
- ✅ Superior user experience

---

## 📊 Monitoring Usage

### **Gemini Usage**

**Monitor at**: https://aistudio.google.com/

- Free tier: 15 requests per minute
- Track: Request count, quota
- Upgrade: If hitting limits

### **Claude Usage**

**Monitor at**: https://console.anthropic.com/settings/usage

- View: API calls, tokens used, costs
- Set alerts: Budget notifications
- Top up: Add more credits

---

## 🔄 Quick Reference Commands

### **Check Current Provider**

```bash
cd C:\xampp\htdocs\opticrew
grep AI_PROVIDER .env
```

### **Switch to Gemini**

```bash
# Edit .env:
AI_PROVIDER=gemini

# Then:
php artisan config:clear
```

### **Switch to Claude**

```bash
# Edit .env:
AI_PROVIDER=claude

# Uncomment CLAUDE_API_KEY line

# Then:
php artisan config:clear
```

### **Test Current Setup**

```bash
# View logs:
tail -f storage/logs/laravel.log

# Then test chatbot in browser
```

---

## ✅ Summary

**Current Status**:
- ✅ Dual AI provider system implemented
- ✅ Gemini set as default (free testing)
- ✅ Claude code preserved (production ready)
- ✅ Easy one-line switching
- ✅ Both use same knowledge base
- ✅ Both have same personality

**Next Steps**:
1. **Get Gemini API key** (free) from https://aistudio.google.com/app/apikey
2. **Add to `.env`**: `GEMINI_API_KEY=your_key`
3. **Clear cache**: `php artisan config:clear`
4. **Test chatbot** on homepage
5. **Later**: Switch to Claude for production

**To Switch to Claude** (when ready):
1. Add Claude API credits
2. Change `.env`: `AI_PROVIDER=claude`
3. Uncomment `CLAUDE_API_KEY`
4. Clear cache
5. Done!

---

**You now have the flexibility to test with free Gemini and upgrade to superior Claude for production!** 🎉
