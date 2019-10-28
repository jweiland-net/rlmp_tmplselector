.. include:: ../Includes.txt


.. _configuration:

Configuration
=============

Basically you have two options to work with the template selector, you can either
put static HTML files into a certain directory and use them as a template or work with pure
TypoScript which means you create cObjects of the type TEMPLATE.

You have to decide which option you prefer, choosing any of them will be fine, but you can
only work with either `files` or `TypoScript`.

The external file way
---------------------

If you prefer working with external HTML files, this is the section you have to read. First thing
you have to do is define the paths which will hold your templates of both the `main` and the `sub`
content. Just create some lines like these in the setup field of your TypoScript template:

.. code-block:: typoscript

   tt_content.list.20.rlmptmplselector_templateselector.settings.templatePathMain = EXT:your_ext_key/Resources/Private/Templates/Main/
   tt_content.list.20.rlmptmplselector_templateselector.settings.templatePathSub = EXT:your_ext_key/Resources/Private/Templates/Sub/

You can add new templates just by putting them into the `Main/` and `Sub/`  directory. All files
with extension like ".htm" or ".html" will be recognized as a template file. Each template may
be accompanied by an icon which will be shown in the template selector. It must have the same
filename as the template file, with the extension ".gif" though. See the reference for
configuration details. You can change the default values by using the template object browser.

The 100% pure TypoScript way
----------------------------

You don't like external HTML files? Prefer working with COAs, wraps and pure TypoScript templates?
Then this is the way you use the template selector:

First you have to create your TS templates like you would normally without the template selector.
Then copy the template objects into the template selector's configuration:

.. code-block:: typoscript

   temp.myFirstTemplate = TEMPLATE
   temp.myFirstTemplate.tx_rlmptmplselector.title = My first template
   temp.myFirstTemplate.tx_rlmptmplselector.imagefile = EXT:site_package/Resources/Public/Icons/First.svg
   temp.myFirstTemplate.template = TEXT
   temp.myFirstTemplate.template.value = <div>###CONTENT###</div>

   temp.mySecondTemplate = FLUIDTEMPLATE
   temp.mySecondTemplate.tx_rlmptmplselector.title = My second template
   temp.mySecondTemplate.tx_rlmptmplselector.imagefile = EXT:site_package/Resources/Public/Icons/Second.svg
   temp.mySecondTemplate.template = TEXT
   temp.mySecondTemplate.template.value = <div>{content}</div>

   tt_content.list.20.rlmptmplselector_templateselector.settings.templateObjects.main {
     10 < temp.myFirstTemplate
     20 < temp.mySecondTemplate
   }

Got the idea? The templates you copy (or link) into templateObjects.main and templateObjects.sub
will appear in the backend's template selector. You can also define a title and an icon path for each
template (and you should).

Now the template selector appears in the backend and you easily can select templates. But how do you
include the template selector into your website's TypoScript? Well, you just forward the template selector's
output to your favourite page object.


Example
-------

.. code-block:: typoscript

   page = PAGE
   page.typeNum = 0

   tt_content.list.20.rlmptmplselector_templateselector.settings.templatePathMain = EXT:site_package/Resources/Private/RlmpTmplSelector/Main/
   tt_content.list.20.rlmptmplselector_templateselector.settings.templatePathSub = EXT:site_package/Resources/Private/RlmpTmplSelector/Sub/

   # No dynamic content. Just print content to page
   page.10 < tt_content.list.20.rlmptmplselector_templateselector

   # Template with dynamic content based on old TEMPLATE Content Object
   page.20 = TEMPLATE
   page.20.template < tt_content.list.20.rlmptmplselector_templateselector
   page.20.marks.CONTENT = TEXT
   page.20.marks.CONTENT.value = Good morning

   # Template with dynamic content based on new FLUIDTEMPLATE Content Object
   page.30 = FLUIDTEMPLATE
   page.30.template < tt_content.list.20.rlmptmplselector_templateselector
   page.30.variables.content = TEXT
   page.30.variables.content.value = Good evening


Reference
---------

.. container:: ts-properties

   =============================== ========== ============================
   Property                        Data type  Default
   =============================== ========== ============================
   templateType_                   String     main
   templatePathMain_               String     fileadmin/template/main
   templatePathSub_                String     fileadmin/template/sub
   defaultTemplateFileNameMain_    String
   defaultTemplateFileNameSub_     String
   defaultTemplateObjectMain_      String     10
   defaultTemplateObjectSub_       String     10
   templateObjects.main_           cObj
   templateObjects.sub_            cObj
   tx_rlmptmplselector.title_      String
   tx_rlmptmplselector.imagefile_  String
   inheritMainTemplates_           Boolean    0
   inheritSubTemplates_            Boolean    0
   =============================== ========== ============================

.. _templateType:

templateType
~~~~~~~~~~~~

Switches between main and sub templates. Possible values: main, sub

.. _templatePathMain:

templatePathMain
~~~~~~~~~~~~~~~~

Paths leading to the directory containing HTML template files. Only makes sense when working
in file mode.

.. _templatePathSub:

templatePathSub
~~~~~~~~~~~~~~~

Paths leading to the directory containing HTML template files. Only makes sense when working
in file mode.

.. _defaultTemplateFileNameMain:

defaultTemplateFileNameMain
~~~~~~~~~~~~~~~~~~~~~~~~~~~

This filename will be used if no template is selected.

By setting this property to different values in different sections of your website you can vary
the default template which will be used.

.. _defaultTemplateFileNameSub:

defaultTemplateFileNameMain
~~~~~~~~~~~~~~~~~~~~~~~~~~~

This filename will be used if no template is selected.

By setting this property to different values in different sections of your website you can vary
the default template which will be used.

.. _defaultTemplateObjectMain:

defaultTemplateObjectMain
~~~~~~~~~~~~~~~~~~~~~~~~~

Defines the default template object which will be used if no template is selected. Only makes
sense in TS mode.

Fx. if you created a TEMPLATE cObject like this:

.. code-block:: typoscript

   templateObjects.sub.10 = TEMPLATE

Then it will automatically be selected if you set defaultTemplateObject to 10

.. _defaultTemplateObjectSub:

defaultTemplateObjectSub
~~~~~~~~~~~~~~~~~~~~~~~~~

Defines the default template object which will be used if no template is selected. Only makes
sense in TS mode.

Fx. if you created a TEMPLATE cObject like this:

.. code-block:: typoscript

   templateObjects.main.10 = TEMPLATE

Then it will automatically be selected if you set defaultTemplateObject to 10

.. _templateObjects.main:

templateObjects.main
~~~~~~~~~~~~~~~~~~~~

Contains your Template Objects if you work in the TS mode (vs. file mode)

.. _templateObjects.sub:

templateObjects.sub
~~~~~~~~~~~~~~~~~~~

Contains your Template Objects if you work in the TS mode (vs. file mode)

.. _tx_rlmptmplselector.title:

tx_rlmptmplselector.title
~~~~~~~~~~~~~~~~~~~~~~~~~

Add this properties to your TEMPLATE cObject.
``title`` will define your template's title which appears in the backend's template selector.
.. _tx_rlmptmplselector.imagefile:

tx_rlmptmplselector.imagefile
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add this properties to your TEMPLATE cObject.
``imagefile`` is the filename of a .gif picture located in the uploads/tf/ folder.

.. _inheritMainTemplates:

inheritMainTemplates
~~~~~~~~~~~~~~~~~~~~

If set to 1 a page which has no template selected (selector showing 'default') will try to
find a template selection above in the rootline, it inherits the template selection.
If there is no selection anywhere in the rootline, the defaultTemplateXXX settings take effect.

.. _inheritSubTemplates:

inheritSubTemplates
~~~~~~~~~~~~~~~~~~~

If set to 1 a page which has no template selected (selector showing 'default') will try to
find a template selection above in the rootline, it inherits the template selection.
If there is no selection anywhere in the rootline, the defaultTemplateXXX settings take effect.
