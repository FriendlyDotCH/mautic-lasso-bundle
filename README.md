# Mautic Lasso Bundle
A Plugin helping you to catch webhooks in Mautic

Status: need fixes
We think, that a plugin, that is able to capture any payload into Mautic represents a great value.
The Lasso plugin supposed to be able to solve this problem.
We developerd this plugin 2 years ago for Mautic 3, and would by happy to port it to Mautic 4 and 5.

The plugin doesn't work at the momment, any help is appreciated.

## Functions

Create your own lasso endpoint, that captures webhooks.

![image](https://github.com/FriendlyDotCH/mautic-lasso-bundle/assets/4246909/e9885485-0073-4aff-aff7-edeed8e88b04)

In the settings you are able to mimic a json payload and decisde what happens with the incoming data. Where it is saved and how is it altered (add, deduct, overwrite)

<img width="1324" alt="Screenshot 2023-07-23 at 16 47 11" src="https://github.com/FriendlyDotCH/mautic-lasso-bundle/assets/4246909/7a0a3e2d-04c1-42d0-ad47-e75b98aa04ad">

The system listens on the URL (for example https://sub.domain.com/mautic/lasso/1)

The user can map how the incoming data is expected, what happens to it, and where it lands.

For example:

Line 1: the json post contains the email of the contact as [shipping][email], we know it’s the ‘key’, so the switch is ‘key’ (probably always for email) and it’s mapped to the email in the system.

Line 2:  the json post contains line items (we have to figure out how to accept more...) which is mapped as a tag (so we will ad the content of this payload as ‘tag’). The add switch will just simply add it. You can’t overwrite tags anyway.

Line 3: [cart][total] is the total cart value, and we have ‘add value’ as switch, which means, that the value is added to the existing value. This way the ‘total spent’ field is will be added the total value. If the ‘total spent’ field was $100 before the webhook, then it will be $100 + payload now.

Line 4: If we choose ‘static’, then a static value is added, which means, it’s not the payload, but the switch determines the value. In this case it’s the current timestamp.

Line 5: [api_psw] we would look for an [api_psw] content is the json. The verification switch makes sure, that if the incoming content is not what we have in the third column, then the call fails.

### Possible switches:

- key (used for email only for now)
- add (used for tags and custom fields with Multiple choice possibility)
- add_value (adds the payload to the current value)
- substract_value (substracts the payload from the current value)
- datetime (overwrites the field content with current datetime)
- date (overwrites the field content with current date)
- a number or string (if you use static payload number or string)
- verification (if the number in the Mautic field is not the incoming payload, then the call fails.)

### Webhook listener URL is found here:

<img width="1064" alt="Screenshot 2023-07-23 at 16 51 08" src="https://github.com/FriendlyDotCH/mautic-lasso-bundle/assets/4246909/c486bf65-116f-48cb-b1dc-f16080f41522">

### Example campaign view:

<img width="1054" alt="Screenshot 2023-07-23 at 16 52 51" src="https://github.com/FriendlyDotCH/mautic-lasso-bundle/assets/4246909/f784b90d-ec86-4994-a763-74e03b1edcd8">

This is how the campaign works:

<img width="1266" alt="Screenshot 2023-07-23 at 16 56 02" src="https://github.com/FriendlyDotCH/mautic-lasso-bundle/assets/4246909/5de69630-7afe-4bd8-bc13-d5248b816cec">


## Log

In the Lasso Data menu you are able to see previous webhook calls.
