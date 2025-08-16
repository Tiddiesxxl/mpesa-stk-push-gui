# Contributing to M-Pesa STK Push GUI

Thank you for your interest in contributing to this project! 🎉

## 🚀 Getting Started

### Prerequisites
- PHP 7.0 or higher
- PHP cURL extension
- SWIFT-WALLET account and API key
- Terminal/Command line access

### Setup for Development

1. **Fork and Clone**
   ```bash
   git clone https://github.com/yourusername/mpesa-stk-push-gui.git
   cd mpesa-stk-push-gui
   ```

2. **Install Dependencies**
   ```bash
   chmod +x install.sh
   ./install.sh
   ```

3. **Configure API Key**
   ```bash
   cp config.sample.php config.php
   # Edit config.php with your SWIFT-WALLET API key
   ```

4. **Test the Application**
   ```bash
   php mpesa_stk_gui.php
   ```

## 🛠️ Development Guidelines

### Code Style
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add comments for complex logic
- Keep functions focused and small

### Testing
- Test all new features thoroughly
- Include error handling for edge cases
- Verify phone number validation works correctly
- Test callback functionality if modified

### Security
- Never commit API keys or sensitive data
- Validate all user inputs
- Use HTTPS for callback endpoints
- Follow secure coding practices

## 📝 How to Contribute

### Reporting Bugs
1. Check existing issues first
2. Create a detailed bug report including:
   - Steps to reproduce
   - Expected vs actual behavior
   - PHP version and environment
   - Error messages or logs

### Suggesting Features
1. Open an issue with the "enhancement" label
2. Describe the feature and its benefits
3. Include mockups or examples if applicable

### Submitting Code
1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Make your changes**
4. **Test thoroughly**
5. **Commit with clear messages**
   ```bash
   git commit -m "Add: New feature description"
   ```
6. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```
7. **Create a Pull Request**

### Commit Message Format
```
Type: Brief description

Detailed explanation if needed

- List specific changes
- Reference issues: Fixes #123
```

**Types:**
- `Add:` New features
- `Fix:` Bug fixes
- `Update:` Improvements to existing features
- `Remove:` Removing code/features
- `Docs:` Documentation changes

## 🎯 Areas for Contribution

### High Priority
- [ ] Payment status polling improvements
- [ ] Additional payment provider integrations
- [ ] Web-based interface
- [ ] Payment scheduling features
- [ ] Enhanced error recovery

### Medium Priority
- [ ] Multi-language support
- [ ] Configuration management improvements
- [ ] Performance optimizations
- [ ] Additional export formats
- [ ] Webhook signature verification

### Low Priority
- [ ] UI/UX enhancements
- [ ] Additional phone number formats
- [ ] Code refactoring
- [ ] Documentation improvements

## 🧪 Testing

### Manual Testing Checklist
- [ ] Phone number validation (01XX, 07XX, 254XX)
- [ ] STK Push initiation
- [ ] Callback handling
- [ ] Error scenarios
- [ ] Bulk payment upload
- [ ] Transaction history
- [ ] Export functionality

### Test Data
Use these test phone numbers (they won't charge):
- `0712345678` - Valid format
- `0112345678` - Valid 01XX format
- `254712345678` - Valid international

## 📚 Resources

### SWIFT-WALLET Documentation
- [API Documentation](https://swiftwallet.co.ke/docs)
- [Dashboard](https://swiftwallet.co.ke/dashboard)

### M-Pesa Resources
- [M-Pesa Developer Portal](https://developer.safaricom.co.ke/)
- [STK Push Documentation](https://developer.safaricom.co.ke/docs#lipa-na-m-pesa-online)

## 🤝 Code of Conduct

### Our Standards
- Be respectful and inclusive
- Focus on constructive feedback
- Help others learn and grow
- Maintain professional communication

### Unacceptable Behavior
- Harassment or discrimination
- Trolling or insulting comments
- Publishing private information
- Spam or off-topic content

## 📞 Getting Help

### Questions?
- Open an issue with the "question" label
- Check existing documentation first
- Be specific about your problem

### Need Support?
- For SWIFT-WALLET API issues: Contact SWIFT-WALLET support
- For application bugs: Open a GitHub issue
- For general questions: Use GitHub Discussions

## 🏆 Recognition

Contributors will be:
- Listed in the README
- Mentioned in release notes
- Given credit for their contributions

## 📄 License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

**Thank you for contributing to M-Pesa STK Push GUI!** 🚀