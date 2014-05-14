rlmp_tmplselector
=================

## Änderungen im Typoscript

In dieser Version gibt es ein paar Änderungen im Typoscript:  

Bisher: (object) < plugin.tx_rlmptmplselector_pi1  
Jetzt: (object) < tt_content.list.20.rlmptmplselector_templateselector  

Bisher: plugin.tx_rlmptmplselector_pi1.templatePathMain =  
Jetzt: tt_content.list.20.rlmptmplselector_templateselector.settings.templatePathMain =   

Bisher: plugin.tx_rlmptmplselector_pi1.templatePathSub =  
Jetzt: tt_content.list.20.rlmptmplselector_templateselector.settings.templatePathSub =   

Bisher: template.templateType = sub
Jetzt: template.settings.templateType = sub

Bisher: template.templateType = main
Jetzt: template.settings.templateType = main