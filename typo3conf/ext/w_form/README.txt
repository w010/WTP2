
* EXAMPLE SETUP:

# Default Name  |  idname = type, [option:value,...]  |  defaultValue  |  validator:value;"Default message", otherValidator
Message  |  message = textarea, rows:5,cols:40  |  |  required:;"message is required", lengthMax:100;"message is too long"
E-mail   |  email = text, size:30  |  |  required:;"email is required", valid:tx_wform_pi1_userfield_email->valid;"invalid email"
Phone    |  phone = text, size:30  | +48 |
Captcha  |  captcha = user, userFunc:tx_wform_pi1_userfield_captcha->getField, type:srfreecap, opt1:abc, opt2:xyz  |  |  required:;"captcha is required", valid:tx_wform_pi1_userfield_captcha->valid;"invalid captcha"


like:
plugin.tx_wform_pi1.formConf(
	setup...
)

and in case of manual integration (look below for INTEGRATE IN OWN EXTENSIONS)
for captcha example field, in Form->addField() we give:
conf = array(
...
	'setup' = array(
		// 'userFunc' => 'tx_wform_pi1_userfield_captcha->getField',	// instead of this, we give it splitted and instantiated:
			'userObj' => t3lib_div::makeInstance('tx_wform_pi1_userfield_captcha'),
			'methodName' => 'getField',
		'type' => 'srfreecap',	// and other options which will be passed to this method
		'opt1' => 'abc',
		'opt2' => 'xyz'


For examples making userfields, check pi1/class.tx_wform_pi1_userfield.php
For more standard WForm validators, look at res/WForm2/class.WFormValidator2.php


! IMPORTANT !

for using ajax, all options from flexform should be set also in ts...
this will be fixed.. somehow




* SPECIFIED INSTANCES
W_form can be also embedded using typoscript. For this you can configure special set of options
for some formular. It is called Specified Instance, with this it's possible
to not set options globally, but for embedded item.
	- Note, that's neccessary (when embedding by typoscript, not as normal ce) to use ajax and can have not global options.
To do so, just write some options in subkey in global config:
plugin.tx_wform_pi1	{
	someOption = x
	specifiedConf.someForm	{
		someOption = y
	}
}
and when embedding, tell w_form to use these:
20 < plugin.tx_wform_pi1
20	{
	specifiedInstance = someForm
	# dont set here any other options, they wont be accessible through ajax
	# use specifiedConf. in global setup.
	# just set key here to use them
}





* LOCALLANG:
All lang values can be set in pi1/locallang.xml using schema:

label_field_[FIELDNAME]  - example:
<label index="label_field_message">Message</label>

msg_field_[FIELDNAME]_[VALIDATORNAME]  - example:
<label index="msg_field_email_required">Email is required</label>

msg_[SOMESYSMESSAGE]  - example:
<label index="msg_formSend">Form was successfuly sent</label>

[OTHER]  - like:
<label index="info_field_captcha">Type above the text from image:</label>
for using in some places like custom fields.


or in TS:

plugin.tx_wform_pi1	{
	_LOCAL_LANG.pl 		{
		label_field_name = Name:

		...

If you need some additional text labels taken from locallang, not rendered as fields etc,
you can use:
plugin.tx_wform_pi1.add_lang_markers = label_mylabel
and in template like ###LABEL_MYLABEL### (strtoupper)




* TEMPLATES:

Default template for every field is subpart ###FIELD_DEFAULT###
may be defined for type - np. ###FIELD_TEXT###
it can be also defined for specified, in case one must differ - ###FIELD__name###

You can generate any existing typo3 content element, using:
###CE_uid### where uid is uid of tt_content record.
To make this work, is neccessary to set option:
plugin.tx_wform_pi1.renderCEuids = [ce uids commalist, to make markers from]




* INTEGRATE WForm2 in own extensions (only lib, not ext):
Look at res/WForm2/example-WForm.php and example-WForm-tmpl.html
It's little old-way example, but starting with this you can easy integrate it.





* MESSAGES:

This ext has 2 sets of messages displayed to user:

pi1->notice
	array, plugin tech notices and from data process
WForm2->error
	array, messages from WForm validators

I assume, that highest priority has pi1 so always the notices will be displayed first.





* HOOKS:

Hooks can be configured in standard way in localconf:
$TYPO3_CONF_VARS['EXTCONF']['w_form']['hook_email_beforeSend'][] = 'user_wform_hooks';
...or using typoscript:
plugin.tx_wform_pi1.hook	{
	# w_form_adds_splash1 is just array index, it does nothing.
	email_beforeSend.w_form_adds_splash1 = user_wform_hooks_splash1
}

@see extension w_form_adds for example hooks.





* DEV INFO: [translate to english]

za pomocą rejestru z referencjami mamy z każdego miejsca dostep do instancji pi1 oraz innych rzeczy.
przypisanie:
[to może powodować konflikt instancji, do sprawdzenia]

$_this = &tx_wform_pi1_registry::Registry('wform', 'instance_pi1');
$_this = $this;

odczyt:
$pObj = &tx_wform_pi1_registry::Registry('wform', 'instance_pi1');




* Known issues:

domyslne walidatory oraz value nie dzialaja dla pol, ktore maja zdefiniowany wlasny prefix (tx_myext_pi1).
z zalozenia form mial miec staly prefix w obrebie jednej instancji, ale czasem moze byc potrzebny dla type = user.
dzieje sie tak, bo obiekt WForm operuje na pivars, a nie na getpost.
trzeba taka walidacje przeprowadzic we wlasnym zakresie, podobnie wypelnienie value.


TODO:

- w ajaxie trzeba przekazywac / wyciagac uid content elementu - bo moze tam byc ustawione Pages (a nie pid w ts)
lub wylaczyc ta mozliwosc.

- i tak trzeba to zrobic, zeby uruchomic pelna obsluge flexform -
	(wtedy w trybie ajax trzeba sparsowac lub wyciagnac te ustawienia)

