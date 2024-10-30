=== Max SMTP - Multiple SMTP Accounts ===
Contributors: effinstudios
Donate link: https://ko-fi.com/effinstudios
Tags: multiple smtp, multiple email accounts, email fallback, backup smtp, wp smtp, wp email, phpmailer
Requires at least: 5.0
Tested up to: 5.6.1
Requires PHP: 5.6
Stable tag: 1.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Never miss an email from your WordPress blog/website with Max SMTP by adding multiple SMTP or email accounts to use as email fallbacks for your WordPress emails.

== Features ==
* Send emails using your preferred SMTP/Email servers.
* Add multiple SMTP/Email accounts to use as fallbacks to avoid failed sent emails from your WordPress blog/website.
* Automatically switches to the next SMTP/Email account if the limit has been reached.
* Queues and retries failed emails from your WordPress blog/website.
* Track your SMTP account usage and monitor failed emails through an easy to use interface.
* Filter and block specified emails from being sent.

== Installation ==
Extract the zip file and just drop the contents in your wp-content/plugins/ directory then activate the Max SMTP from your wp-admin/plugins page.

== Frequently Asked Questions ==
= Can I use my Gmail/Yahoo/Web Hosting email account? =
* Yes, as long as you have the SMTP server settings yoiu can configure and save your SMTP server account details for use on your WordPress blog/website.

= Can I use this with Ninja Forms/WooCommerce/Contact Form 7/Gravity Forms? =
* Yes, Max SMTP is genreally compatible with other plugins that uses the standard wp_mail function.

== Screenshots ==
* Please visit our website - [Effin Studios](https://effinstudios.com/wp-plugins/max-smtp-multiple-smtp-accounts) for screenshots.

== Changelog ==

= Version 1.1.3 =
* Added automatic daily sent email queue clearing.

= Version 1.1.2 =
* Minor bug fixes.
* Added filter hooks "maxsmtp_filter_mail_body" and "maxsmtp_filter_mail_subject" to filter body or subject string before it is sent.

= Version 1.1.1 =
* New feature that allows you to stop specified emails from being sent out.
* New tabbed settings page.
* Fixed queued emails not being sent through default WP Mail settings if user did not add SMTP account.

= Version 1.1.0 =
* Added feature to view queued email content.
* Added hooks "maxsmtp_filter_smtp_settings" to filter the SMTP settings array before it is set and "maxsmtp_filter_email_queue_before_save" to filter the failed email array before it is saved to the email queue.

= Version 1.0.9 =
* Improved user input security.

= Version 1.0.8 =
* Initial public release.

== Upgrade Notice ==

= Version 1.1.3 =
Upgrade to Version 1.1.3 to remove issues with email queue database bloating which remove sent mails from the database.