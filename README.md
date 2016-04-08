bihang-whmcs-plugin
==================

# Installation

Extract these files into your whmcs directory (parent directory of
modules/folder)

# Configuration

1. Check that you have set your Domain and WHMCS System URL under whmcs/admin > Setup > General Settings
2. Create an API Key and Secret in your bihang account at bihang.com.
3. In the admin control panel, go to "Setup" > "Payment Gateways", select
   "bihang" in the list of modules and click Activate.
4. Enter your API Key and Secret from step 2. 
5. Click "Save Changes."

# Usage

When a client chooses the bihang payment method, they will be presented with an
invoice showing a button to pay the order.  Upon requesting to pay their order,
the system takes the client to a bihang.com invoice page where the client is
presented with bihang payment instructions.  Once payment is received, a link
is presented to the shopper that will return them to your website.

In your Admin control panel, you can see the information associated with each
order made via bihang ("Orders" > "Pending Orders").  This screen will tell
you whether payment has been received by the bihang servers.  


## WHMCS Support

* [Homepage](https://www.whmcs.com/)
* [Documentation](http://docs.whmcs.com/Main_Page)
* [SupportForums](http://forum.whmcs.com/)

# Contribute

To contribute to this project, please fork and submit a pull request.
