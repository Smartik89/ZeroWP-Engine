
              |  zwe__nonce              |  zwe__module   |  zwe__form_id           |  zwe__action            |  zwe__id     |

------------------------------------------------------ Backend ---------------------------------------------------------------

OptionsPage:  |  zwe_OptionsPage_nonce   |  OptionsPage   |  {$options_page_id}     |  zwe_OptionsPage_save   |  ""          |
Metabox:      |  zwe_Metabox_nonce       |  Metabox       |  zwe-{$post_type}-form  |  zwe_Metabox_save       |  {$post_id}  |
Widget:       |  zwe_Widget_nonce        |  Widget        |  {$widget_id}           |  zwe_Widget_save        |  ""          |
UserForm:     |  zwe_UserForm_nonce      |  UserForm      |  zwe-userform           |  zwe_UserForm_save      |  {$user_id}  |
TaxonomyForm: |  zwe_TaxonomyForm_nonce  |  TaxonomyForm  |  zwe-{$taxonomy}-form   |  zwe_TaxonomyForm_save  |  ""          |

----------------------------------------------------- Frontend ---------------------------------------------------------------

AuthLogin     |  zwe_AuthLogin_nonce     |  AuthLogin     |  zwe-AuthLogin-form     |  zwe_AuthLogin_save     |  ""          |
UserSettings  |  zwe_UserSettings_nonce  |  UserSettings  |  zwe-UserSettings-form  |  zwe_UserSettings_save  |  {$user_id}  |
AuthRegister  |  zwe_AuthRegister_nonce  |  AuthRegister  |  zwe-AuthRegister-form  |  zwe_AuthRegister_save  |  {$user_id}  |
PostForm      |  zwe_PostForm_nonce      |  PostForm      |  zwe-{$post_type}-form  |  zwe_PostForm_save      |  {$post_id}  |
GeneralForm   |  zwe_GeneralForm_nonce   |  GeneralForm   |  zwe-GeneralForm-form   |  zwe_GeneralForm_save   |  ""          |
