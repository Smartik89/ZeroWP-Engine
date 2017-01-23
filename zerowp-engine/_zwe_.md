```
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
```
<!--
<table>
	<tr>
		<th></th>
		<th>zwe__nonce</th>
		<th>zwe__module</th>
		<th>zwe__form_id</th>
		<th>zwe__action</th>
		<th>zwe__id</th>
	</tr>
	<tr>
		<th colspan="6">Backend</th>
	</tr>
	<tr>
		<td>OptionsPage:</td>
		<td>zwe_OptionsPage_nonce</td>
		<td>OptionsPage</td>
		<td>{$options_page_id}</td>
		<td>zwe_OptionsPage_save</td>
		<td>""</td>
	</tr>
	<tr>
		<td>Metabox:</td>
		<td>zwe_Metabox_nonce</td>
		<td>Metabox</td>
		<td>zwe-{$post_type}-form</td>
		<td>zwe_Metabox_save</td>
		<td>{$post_id}</td>
	</tr>
	<tr>
		<td>Widget:</td>
		<td>zwe_Widget_nonce</td>
		<td>Widget</td>
		<td>{$widget_id}</td>
		<td>zwe_Widget_save</td>
		<td>""</td>
	</tr>
	<tr>
		<td>UserForm:</td>
		<td>zwe_UserForm_nonce</td>
		<td>UserForm</td>
		<td>zwe-userform</td>
		<td>zwe_UserForm_save</td>
		<td>{$user_id}</td>
	</tr>
	<tr>
		<td>TaxonomyForm:</td>
		<td>zwe_TaxonomyForm_nonce</td>
		<td>TaxonomyForm</td>
		<td>zwe-{$taxonomy}-form</td>
		<td>zwe_TaxonomyForm_save</td>
		<td>""</td>
	</tr>

	<tr>
		<th colspan="6">Frontend</th>
	</tr>
	<tr>
		<td>AuthLogin</td>
		<td>zwe_AuthLogin_nonce</td>
		<td>AuthLogin</td>
		<td>zwe-AuthLogin-form</td>
		<td>zwe_AuthLogin_save</td>
		<td>""</td>
	</tr>
	<tr>
		<td>UserSettings</td>
		<td>zwe_UserSettings_nonce</td>
		<td>UserSettings</td>
		<td>zwe-UserSettings-form</td>
		<td>zwe_UserSettings_save</td>
		<td>{$user_id}</td>
	</tr>
	<tr>
		<td>AuthRegister</td>
		<td>zwe_AuthRegister_nonce</td>
		<td>AuthRegister</td>
		<td>zwe-AuthRegister-form</td>
		<td>zwe_AuthRegister_save</td>
		<td>{$user_id}</td>
	</tr>
	<tr>
		<td>PostForm</td>
		<td>zwe_PostForm_nonce</td>
		<td>PostForm</td>
		<td>zwe-{$post_type}-form</td>
		<td>zwe_PostForm_save</td>
		<td>{$post_id}</td>
	</tr>
	<tr>
		<td>GeneralForm</td>
		<td>zwe_GeneralForm_nonce</td>
		<td>GeneralForm</td>
		<td>zwe-GeneralForm-form</td>
		<td>zwe_GeneralForm_save</td>
		<td>""</td>
	</tr>
</table>
-->