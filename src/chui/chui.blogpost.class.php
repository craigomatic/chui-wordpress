<?php
    class BlogPost {
        public $Title = '';
        public $Content = '';
		public $Excerpt = '';
        public $Id = 0;
		public $Slug = '';
        
        function GetMenuView() {
            return 'This is the synopsis';
        }
        
        function GetDetailView() {
            return 'This is the detail view';
        }
    }
?>