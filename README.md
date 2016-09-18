# formidable_key_field
Add two field to formidable with the ability to generate a random string and then validate from other form.

##Description##

This plugins add two new fields to formidable. With one you generate and random string, with the other you can validate if the given string exist in other form.

##Example##
You need to send and security key to the user, and validate in other form if the key is valid. Then you create two form one to generate the random string and other to validate.
In the first form you need to configure the field generator with the length of the string and if you grant special chars.
In the second form you need to add the validation field and select the form target, and the validation type.
