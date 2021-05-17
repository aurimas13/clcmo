/*global alert, ajaxurl, console */
(function($){
	"use strict";

	var $body;

	window.A13_REE_ADMIN = {
		//run after DOM is loaded
		onReady : function(){
			$body = $(document.body);
			this.tabs();
			this.importer();
		},

		tabs : function(){
			var tabs = $('nav.nav-tab-wrapper').children();

			if (tabs.length) {
				var sections = $('div.a13-settings-section'),
					active_tab_class = 'nav-tab-active',
				    active_section_class = 'active-section',

					tabsClick               = function (event) {
						event.preventDefault();

						var hrefWithoutHash = location.href.replace(/#.*/, '');

						history.pushState({}, '', hrefWithoutHash + this.hash);

						goToSettingsTabFromHash();
					},

					goToSettingsTabFromHash = function () {
						var hash = location.hash.slice(1);

						if (hash) {
							goToSettingsTab(hash);
						}
					},

					goToSettingsTab         = function (tabName) {
						var $active_section = $('#'+tabName),
							$active_tab = $('#a13-settings-'+tabName);

						if (!$active_section.length || !sections.is($active_section)) {
							//use first tab if hash is wrong
							$active_section = sections.eq(0);
							$active_tab = tabs.eq(0);
						}
						//switch tabs
						tabs.removeClass(active_tab_class);
						$active_tab.addClass(active_tab_class);

						//switch sections
						sections.removeClass(active_section_class);
						$active_section.addClass(active_section_class);
					};

				//bind events
				tabs.on({
					click: tabsClick
				});

				//init
				goToSettingsTabFromHash();

				//history moving
				$(window).on('popstate', function () {
					var hash = location.hash.slice(1);
					if(hash.length && sections.is('#'+hash)){
						goToSettingsTab(hash);
					}
				});
			}
		},

		importer : function(){
			var import_button = $('#start-import'),
				progress_bar  = $('div.import_progress'),
				status = $('#demo_data_import_status'),
				error_count,last_response,

				startImport = function(){
					//reset
					error_count = 0;
					last_response = {
						level : '',
						sublevel:  ''
					};

					import_button.prop('disabled', true);

					//start progress bar
					progress_bar.addClass('running');

					//start import
					nextLevel('','');
				},

				nextLevel = function(level, sublevel){
					$.ajax({
						type: "POST",
						url: ajaxurl,
						data:  {
							action : 'a13ree_import_templates', //called in backend
							level : level,
							sublevel : sublevel
						},
						success: function(r) { //r = response
							if(r !== false ){
								//save last reply
								last_response = r;
								//reset error counter
								error_count = 0;

								setupStatus(r);

								if(r.is_it_end === false){//end of importing
									nextLevel(r.level, r.sublevel);
								}
								else{
									progress_bar.css('width','100%').removeClass('running');
									import_button.prop('disabled', false );
								}
							}
						},
						error: function(  jqXHR,  textStatus,  errorThrown ){
							var message;

							//check what type of error happened
							if(typeof jqXHR.status !=='undefined' && (jqXHR.status == 404 || jqXHR.status == 403)){
								message = 'Server returned status '+jqXHR.status+'. Hopefully it is temporary.';
							}
							else if(textStatus == 'parsererror'){
								message = 'Importer returned data in wrong format. Probably unexpected HTML leaked into server reply instead of JSON format.';
							}
							else if( errorThrown == 'Internal Server Error' ){
								message = 'Server returned "Internal Server Error" while importing. It usually mean misconfiguration in server/WordPress.';
							}
							else{
								message = 'Unknown error: '+textStatus+' - '+errorThrown;
							}

							//try to recover from error
							if( error_count < 10 ){
								//count this error
								error_count++;

								//wait and try again
								setTimeout(function(){nextLevel(last_response.level, last_response.sublevel);}, 5000);

								//done here
								return;
							}

							//report error
							alert( message );

							progress_bar.removeClass('running');
							import_button.prop('disabled', true);
						},
						dataType: 'json'
					});
				},

				setupStatus = function(r){
					var content = r.level_name;
					if(r.sublevel_name.length){
						content += ' - '+r.sublevel_name;
					}

					status.html(content);
					progress_bar.css('width',r.progress+'%');

					if(r.alert == true){
						alert(r.log);
					}
				};

			import_button.on( 'click', startImport);
		}
	};

	var A13_REE_ADMIN = window.A13_REE_ADMIN;

	//start ADMIN
	$( function() {
		A13_REE_ADMIN.onReady();
	} );

})(jQuery);