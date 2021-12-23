# GOFAS Auto Login WHMCS


## Create magic links in email templates for
Access invoices and tickets with just one click.

### No need to configure and edit code, you just need to activate
The module resets the links generated by the standard WHMCS mergetags{$invoice_link}in the invoice email templates and the links generated by the mergetag `{$ticket_link}` in the ticket templates.


### Offers additional mergetag for free formatting of emails
You can also choose to use the mergetag `{$login_link}` in the invoice and ticket email templates if you want to keep the default WHMCS behavior, or avoid conflicts with modules and hooks that reset the value of these mergetags.

## Interface	

https://www.awesomescreenshot.com/image/19020120?key=8ca283228fc4ace12d58421b4be5bffd

## Requirements
PHP 7.1+
WHMCS 8.0.+
The system requirements were defined according to our tests, if your system doesn't fit the requirements, it doesn't mean the module won't work for your whmcs, it just means that we didn't test in the same environment.

## Installation
- Upload the `/gofasautologin/` directory to the default WHMCS addons directory: `/modules/addons/`.
- Activate the module, enter your license key and click save.

Important: In addition to the `{$invoice_link}` tag in invoice templates and the `{$ticket_link}` tag in ticket templates, this option must be enabled in the customer's profile:

https://s3.amazonaws.com/uploads.gofas.me/wp-content/uploads/2020/10/21165448/whmcs_single_sign_on_perfil_cliente.png

## Changelog Collapse

401 - Bad credentials.
Please review access token for user mauriciogofas

v3.2.1
02/08/2021

User-Agent in the request header when checking for updates on gofas.net;
v3.2.0
01/24/2021
New option: Use default WHMCS merge tag `{$invoice_link}` or use custom mergetag `{$login_link}` in email templates. Avoid conflicts with modules and hooks that also reset the value of the mergetag `{$invoice_link}`;
v3.1.0
10/22/2020

Support for credit card email templates. It works perfectly together with the Juno Card module for WHMCS;
New option: Debug. Saves diagnostic information to the Module Log when links are generated and accessed.
New option: WHMCS Administrator. Defines the administrator with permissions to use the built-in WHMCS API. Avoid errors caused by changes introduced in the internal API of WHMCS v8.+
Customer readable information (name, email, etc) is now encoded in such a way that it cannot be non-"human readable".
v3.0.0
10/8/2020

The old AutoAuth authentication method has been replaced by the new, more secure WHMCS Single Sign-On method.
Compatibility with WHMCS 8.0.+;
Eliminated the need to edit module code;
Eliminated the need to edit email templates;
v2.2
Compatibility with WHMCS 7.1.2 and adaptation to the guidelines of the new developer documentation;
Fixed bug preventing automatic login to specific ticket URLs;
Introduction of dynamic tag `{$debug}` which displays the result of script execution and diagnostic information in ticket and invoice emails.

**Desenvolvido por Mauricio Gofas
https://gofas.net/whmcs/whmcs-auto-login/
