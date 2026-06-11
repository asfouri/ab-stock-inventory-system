# Security Policy

This project includes inventory, sales, user accounts, role-based access, and database operations. Security is part of the product quality bar.

## Reporting Issues

Please report sensitive vulnerabilities privately.

Include:

- Affected file or module
- Steps to reproduce
- Possible impact
- Suggested fix, if available

## Security Priorities

- Password hashing
- Prepared database queries
- CSRF protection
- Role-based access control
- Safe session handling
- No committed secrets
- Clear environment configuration

## Production Hardening

Before production deployment:

- Move credentials to environment variables
- Add automated tests for access control
- Enable dependency scanning
- Review file permissions
- Run CI checks on every pull request
