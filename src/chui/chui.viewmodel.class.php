<?php
    class ViewModel {
        public $RequestedPage = 0;
		public $RequestedPageTitle = '';
		public $IsFrontPage = false;
		public $FrontPageId = 0;
        public $BlogPosts = array();
		public $Pages = array();
		public $MenuOrder = MenuOrder::ContentThenMenu;
		
		function render_menu($front_page_content = '') {

			switch($this->MenuOrder) {
				case MenuOrder::ContentThenMenu: {
					
					echo "<div ui-kind='grouped'>" . $front_page_content . "</div>";
					$this->render_menu_internal();
					
					break;
				}            
				case MenuOrder::MenuThenContent: {
					
					$this->render_menu_internal();
					echo "<div ui-kind='grouped'>" . $front_page_content . "</div>";
					
					break;
				}
			}
		}
		
		private function render_menu_internal() {
			
			echo "<tableview>";			
			array_walk($this->Pages, "chui_write_menu_items", $this);			
			echo "</tableview>";
		}
    }
?>