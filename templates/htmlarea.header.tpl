<script type="text/javascript">
    // You must set _editor_url to the URL (including trailing slash) where
    // xinha is installed, it's highly recommended to use an absolute URL
    //  eg: _editor_url = "/path/to/xinha/";
    // In the case of this template we use the BASE_DIR setting specified in
    // the config file and mapped to Smarty.
    _editor_url  = "{$BASE_DIR}/edc/";
    _editor_lang = "en";      // And the language we need to use in the editor.
</script>
<!-- Load up the actual editor core -->
<script type="text/javascript" src="{$BASE_DIR}/edc/htmlarea.js"></script>
<script type="text/javascript">
{literal}
    xinha_editors = null;
    xinha_init    = null;
    xinha_config  = null;
    xinha_plugins = null;

    // This contains the names of textareas we will make into Xinha editors
    xinha_init = xinha_init ? xinha_init : function()
    {
      /** STEP 1 ***************************************************************
       * First, what are the plugins you will be using in the editors on this
       * page.  List all the plugins you will need, even if not all the editors
       * will use all the plugins.
       ************************************************************************/

      xinha_plugins = xinha_plugins ? xinha_plugins :
      [
        'InsertPicture',
        'InsertCmsFileLink',
        'Stylist'
      ];

      // THIS BIT OF JAVASCRIPT LOADS THE PLUGINS, NO TOUCHING  :)
      if ( ! HTMLArea.loadPlugins(xinha_plugins, xinha_init) )
      {
        // The call above fails in some cases but this is normally not fatal
        // as the function execution is just postponed and the whole page is
        // read once again (?).
        return;
      }
      
      // Setup image browser parameters
      if ( typeof InsertPicture != 'undefined' )
      {
        // InsertPicture object has been created
{/literal}
        InsertPicture.objid = -1{$id};
        InsertPicture.ftype = -1{$imgftype};
        InsertPicture.base  = '{$SCRIPT_NAME}';
{literal}
      }

      /** STEP 2 ***************************************************************
       * Now, what are the names of the textareas you will be turning into
       * editors?
       ************************************************************************/

      xinha_editors = xinha_editors ? xinha_editors :
      [
        'edcTextArea'
      ];

      /** STEP 3 ***************************************************************
       * We create a default configuration to be used by all the editors.
       * If you wish to configure some of the editors differently this will be
       * done in step 4.
       *
       * If you want to modify the default config you might do something like this.
       *
       *   xinha_config = new HTMLArea.Config();
       *   xinha_config.width  = 640;
       *   xinha_config.height = 420;
       *
       *************************************************************************/

      xinha_config = xinha_config ? xinha_config : new HTMLArea.Config();
	  xinha_config.pageStyle = 'body { font-family: Arial,Helvetica,sans-serif; font-size: 12px; }';
	   
/*
       // We can load an external stylesheet like this - NOTE : YOU MUST GIVE AN ABSOLUTE URL
      //  otherwise it won't work!
      xinha_config.stylistLoadStylesheet(document.location.href.replace(/[^\/]*\.html/, 'stylist.css'));

      // Or we can load styles directly
      xinha_config.stylistLoadStyles('p.red_text { color:red }');

      // If you want to provide "friendly" names you can do so like
      // (you can do this for stylistLoadStylesheet as well)
      xinha_config.stylistLoadStyles('p.pink_text { color:pink }', {'p.pink_text' : 'Pretty Pink'});
*/
      xinha_config.stylistLoadStylesheet(document.location.href.replace(/[^\/]*\.php/, 'stylist.css'));
      
      /* Create a customised toolbar. */

      xinha_config.toolbar =
      [
        ["popupeditor"],
        ["separator","formatblock","fontname","fontsize","bold","italic","underline","strikethrough"],
        ["separator","forecolor","hilitecolor","textindicator"],
        ["separator","subscript","superscript"],
        ["linebreak","separator","justifyleft","justifycenter","justifyright","justifyfull"],
        ["separator","insertorderedlist","insertunorderedlist","outdent","indent"],
        ["separator","inserthorizontalrule","createlink","insertcmsfilelink","insertimage","inserttable"],
        ["separator","undo","redo","selectall","print"], (HTMLArea.is_gecko ? [] : ["cut","copy","paste","overwrite","saveas"]),
        ["separator","killword","clearfonts","removeformat","toggleborders","splitblock","lefttoright", "righttoleft"],
        ["separator","htmlmode","showhelp","about"]
      ];


      /** STEP 3 ***************************************************************
       * We first create editors for the textareas.
       *
       * You can do this in two ways, either
       *
       *   xinha_editors   = HTMLArea.makeEditors(xinha_editors, xinha_config, xinha_plugins);
       *
       * if you want all the editor objects to use the same set of plugins, OR;
       *
       *   xinha_editors = HTMLArea.makeEditors(xinha_editors, xinha_config);
       *   xinha_editors['myTextArea'].registerPlugins(['Stylist','FullScreen']);
       *   xinha_editors['anotherOne'].registerPlugins(['CSS','SuperClean']);
       *
       * if you want to use a different set of plugins for one or more of the
       * editors.
       ************************************************************************/

      xinha_editors   = HTMLArea.makeEditors(xinha_editors, xinha_config, xinha_plugins);

      /** STEP 4 ***************************************************************
       * If you want to change the configuration variables of any of the
       * editors,  this is the place to do that, for example you might want to
       * change the width and height of one of the editors, like this...
       *
       *   xinha_editors.myTextArea.config.width  = 640;
       *   xinha_editors.myTextArea.config.height = 480;
       *
       ************************************************************************/


      /** STEP 5 ***************************************************************
       * Finally we "start" the editors, this turns the textareas into
       * Xinha editors.
       ************************************************************************/

      HTMLArea.startEditors(xinha_editors);
      /** nope,nope, we have our own ON_LOAD handler */
	  // window.onload = null;
    }

	/* Add an onLoad handler. */
    ON_LOAD[ON_LOAD.length] = xinha_init;
</script>
{/literal}
