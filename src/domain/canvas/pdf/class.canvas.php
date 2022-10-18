<?php
/**
 * Template - HTML code for PDF report
 */

namespace leantime\domain\pdf {
    
    use leantime\core;
    use leantime\domain\repositories;
    
    /**
     * Template class for generating PDF reports
     */           
    class canvas {
        
        /**
         * Constant that must be redefined
         */
        protected const CANVAS_NAME = '??';

        // Color constants
        public const PDF_COLOR_BG = '#f4f4f6';           // Background color of canvas boxes
        public const PDF_COLOR_BG_TITLE = '#fefefe';     // Background color of list box titles
        public const PDF_COLOR_BG_SUBTITLE = '#f5f4f3';  // Background color of canvas box titles

        // Page layout constants
        public const PDF_A3 = 'A3';
        public const PDF_A4 = 'A4';
        public const PDF_PORTRAIT = 'P';
        public const PDF_LANDSCAPE = 'L';
        public const PDF_CANVAS_A3_HEIGHT = 450;
        
        private const PDF_MARGIN = 30;
        private const PDF_MARGIN_TOP = 90;
        private const PDF_MARGIN_BOTTOM = 70;
        private const PDF_MARGIN_HEADER = 0;
        private const PDF_MARGIN_FOOTER = 0;
        private const PDF_HEADER_HEIGHT = 75;
        private const PDF_FOOTER_HEIGHT = 50;
        private const PDF_HEADER_ROW_HEIGHT = 25;
        
        // Internal variables
        protected core\config $config;
        protected core\language $language;
		protected $canvasRepo;
		protected array $canvasTypes;
		protected array $statusLabels;
		protected array $relatesLabels;
		protected array $dataLabels;
        protected array $params;
        protected int $fontSize;
        protected int $fontSizeLarge;
        protected int $fontSizeTitle;
        protected int $fontSizeSmall;
        protected array $filter;
        
        
        /***
         * Constructor
         */
        public function __construct()
        {
            
            $this->config = new core\config();
            $this->language = new core\language();
			$canvasRepoName = "\\leantime\\domain\\repositories\\".static::CANVAS_NAME."canvas";
			$this->canvasRepo = new $canvasRepoName();

			$this->canvasTypes = $this->canvasRepo->getCanvasTypes();
			$this->statusLabels = $this->canvasRepo->getStatusLabels();
			$this->relatesLabels = $this->canvasRepo->getRelatesLabels();
			$this->dataLabels = $this->canvasRepo->getDataLabels();
            
            // Set default parameters
            $this->params = [ 'disclaimer' => '', 'fontSize' => 10, 'fontSizeLarge' => 11, 'fontSizeSmall' => 8, 'fontSizeTitle' => 12,
                              'canvasShow' => true, 'canvasSize' => self::PDF_A3, 'canvasOrientation' => self::PDF_LANDSCAPE,
                              'canvasHeight' => self::PDF_CANVAS_A3_HEIGHT,
                              'listShow' => true,'listSize' => self::PDF_A4, 'listOrientation' => self::PDF_PORTRAIT,
							  'elementStatus' => 'label.status', 'elementRelates' => 'label.relates',
                              ];
            
        }
        
        /**
         * htmlReport - Generate report in HTML format
         *
         * @access public
         * @param  string $projectHeader Name of the project (goes to header / right)
         * @param  string $moduleTitle   Name of the canvas to be displayed (goes to header / centered)
         * @param  array  $recordsAry    Canvas data
         * @param  array  $filter        Filters (either [] for no filter, or filters in ['status'] and ['relates'])
         * @param  array  $options       Array of paramters to be overwritten (optional)
         * @return string HTML code
         */
        public function htmlReport(string $projectTitle, string $moduleTitle, array $recordsAry, array $filter = [],
								   array $options = []): string
        {
            
            // Set options
            foreach($options as $key => $value) {
                $this->params[$key] = $value;
            }
            $this->filter = $filter;
            
            // Initialize HTML page
            $html = $this->htmlInit();
            
            // Layout canvas page
            if($this->params['canvasShow']) {
                $html .= $this->htmlCanvasOpen();
                $html .= $this->htmlStyles();
                $html .= $this->htmlHeader($projectTitle, $moduleTitle);
                $html .= $this->htmlFooter($this->language->__("headline.".static::CANVAS_NAME.".board"), $this->params['disclaimer']);
                $html .= '<div style="height: '.$this->params['canvasHeight'].'px;">';
                $html .= $this->htmlCanvas($recordsAry);
                $html .= '</div>';
                $html .= $this->htmlPageClose();
            }
            
            // Layout list of element details
            if($this->params['listShow']) {
                $html .= $this->htmlListOpen();
                $html .= $this->htmlStyles();
                $html .= $this->htmlHeader($projectTitle, $moduleTitle);
                $html .= $this->htmlFooter($this->language->__("headline.".static::CANVAS_NAME.".board"), $this->params['disclaimer']);
                $html .= $this->htmlList($recordsAry);
                $html .= $this->htmlPageClose();
            }
            
            $html .= $this->htmlEnd();
            return $html;
            
        }
        
        /**
         * htmlCanvasStatus - Return HTML code showing status of canvas element
         *
         * @access protected
         * @param  string $status Element status key
         * @return string HTML code
         */
        protected function htmlCanvasStatus(string $status): string
        {
			
            return '<span style="color : '.$this->statusLabels[$status]['color'].'">'.
                $this->htmlIcon($this->statusLabels[$status]['icon']).'</span>';

        }
    
        /**
         * htmlListStatus - Return HTML code showing status of list element
         *
         * @access protected
         * @param  string $status Element status key
         * @return string HTML code
         */
        protected function htmlListStatus(string $status): string
        {

            return '<span style="color : '.$this->statusLabels[$status]['color'].'">'.$this->statusLabels[$status]['title'].'</span>';

        }

        /**
         * htmlCanvasRelates - Return HTML code showing relates of canvas element
         *
         * @access protected
         * @param  string $relates Element relates key
         * @return string HTML code
         */
        protected function htmlCanvasRelates(string $relates): string
        {
			
            return '<span style="color : '.$this->relatesLabels[$relates]['color'].'">'.
                $this->htmlIcon($this->statusLabels[$relates]['icon']).'</span>';

        }
    
        /*
         * htmlListRelates - Return HTML code showing relates of list element
         *
         * @access protected
         * @param  string $relates Element status key
         * @return string HTML code
         */
        protected function htmlListRelates(string $relates): string
        {

            return '<span style="color : '.$this->relatesLabels[$relates]['color'].'">'.$this->relatesLabels[$relates]['title'].'</span>';

        }

        /**
         * htmlCanvas -  Layout canvas (must be implemented)
         *
         * @access public
         * @param  array  $recordsAry Array of canvas data records
         * @return string HTML code
         */
        protected function htmlCanvas(array $recordsAry): string
        {
			
            return 'NOT IMPLEMENTED';

        }
        
        /**
         * htmlList - Layout element list (must be implemented)
         *
         * @access public
         * @param  array  $recordsAry Array of canvas data records
         * @return string HTML code
         */
        protected function htmlList(array $recordsAry): string
        {

            return $this->htmlListDetailed($recordsAry);

        }
        
        /**
         * htmlListCompact - Layout element list in a compact form
         *
         * @access public
         * @param  array  $recordsAry Array of canvas data records
         * @return string HTML code
         */
        protected function htmlListCompact(array $recordsAry): string
        {
			
			$html = '';
			foreach($this->canvasTypes as $key => $data) {
				$html .= '<div>'.$this->htmlListTitle($data['title'], $data['icon']).'</div>';
				$html .= '<div>'.$this->htmlListElementsCompact($recordsAry, $key).'</div>';
			}
            return $html;

        }
        
        /**
         * htmlListDetailed - Layout element list in a detailed form
         *
         * @access public
         * @param  array  $recordsAry Array of canvas data records
         * @return string HTML code
         */
        protected function htmlListDetailed(array $recordsAry): string
        {

			$html = '';
			foreach($this->canvasTypes as $key => $data) {
				$html .= '<div>'.$this->htmlListTitle($data['title'], $data['icon']).'</div>';
				$html .= '<div>'.$this->htmlListElementsDetailed($recordsAry, $key).'</div>';
			}
            return $html;

        }
        
        /**
         * htmlInit - Initialize HTML page
         *
         * @access protected
         * @return string HTML code
         */
        protected function htmlInit(): string
        {
            
            // Define font sizes
            $this->fontSize = $this->params['fontSize'];
            $this->fontSizeLarge = $this->params['fontSizeLarge'];
            $this->fontSizeTitle = $this->params['fontSizeTitle'];
            $this->fontSizeSmall = $this->params['fontSizeSmall'];
            
            // Load default font
            \YetiForcePDF\Document::addFonts([
                ['family' => 'Roboto', 'weight' => '400', 'style' => 'normal', 'file' => ROOT.'/fonts/roboto/Roboto-Regular.ttf'],
                ['family' => 'Roboto', 'weight' => '400', 'style' => 'italic', 'file' => ROOT.'/fonts/roboto/Roboto-Italic.ttf'],
                ['family' => 'Roboto', 'weight' => 'bold', 'style' => 'normal', 'file' => ROOT.'/fonts/roboto/Roboto-Bold.ttf'],
                ['family' => 'Roboto', 'weight' => 'bold', 'style' => 'italic', 'file' => ROOT.'/fonts/roboto/Roboto-BoldItalic.ttf']
            ]);
      
            // Load condensed font
            \YetiForcePDF\Document::addFonts([
                ['family' => 'RobotoCondensed', 'weight' => '400', 'style' => 'normal', 
                 'file' => ROOT.'/fonts/roboto/RobotoCondensed-Regular.ttf'],
                ['family' => 'RobotoCondensed', 'weight' => '400', 'style' => 'italic', 
                 'file' => ROOT.'/fonts/roboto/RobotoCondensed-Italic.ttf'],
                ['family' => 'RobotoCondensed', 'weight' => 'bold', 'style' => 'normal', 
                 'file' => ROOT.'/fonts/roboto/RobotoCondensed-Bold.ttf'],
                ['family' => 'RobotoCondensed', 'weight' => 'bold', 'style' => 'italic',
                 'file' => ROOT.'/fonts/roboto/RobotoCondensed-BoldItalic.ttf']
            ]);

            // Load FontAwsome icon font
            \YetiForcePDF\Document::addFonts([
                ['family' => 'FontAwesome', 'weight' => '400', 'style' => 'normal', 
				 'file' => ROOT.'/css/libs/fontawesome-free/webfonts/fa-regular-400.ttf'],
                ['family' => 'FontAwesome', 'weight' => '900', 'style' => 'normal', 
				 'file' => ROOT.'/css/libs/fontawesome-free/webfonts/fa-solid-900.ttf']
            ]);

            // Start document
            $html = '<div>';
            return $html;
            
        }

        /**
         * htmlEnd - Terminate HTML code
         *
         * @access protected
         * @return string HTML code
         */
        protected function htmlEnd(): string
        {
            
            $html = '</div>';
            return $html;
            
        }
        
        /**
         * htmlCanvasOpen() - Start landscape for canvas page
         *
         * @access protected
         * @return string HTML code
         */
        protected function htmlCanvasOpen(): string
        {
            
            $html = '<div data-page-group data-format="'.$this->params['canvasSize'].'" '.
                'data-orientation="'.$this->params['canvasOrientation'].'" '.
                'data-margin-left="'.self::PDF_MARGIN.'" data-margin-right="'.self::PDF_MARGIN.'" '.
                'data-margin-top="'.self::PDF_MARGIN_TOP.'" data-margin-bottom="'.self::PDF_MARGIN_BOTTOM.'" '.
                'data-header-top="'.self::PDF_MARGIN_HEADER.'" data-footer-bottom="'.self::PDF_MARGIN_FOOTER.'"></div>';
            $html .= '<div style="font-family: \'Roboto\'; font-weight: 400; font-style: normal; font-size: '.$this->fontSize.'px">';
            return $html;
            
        }

        /**
         * htmlListOpen -  Start page for list of canvas elements
         *
         * @access protected
         * @return string HTML code
         */
        protected function htmlListOpen(): string
        {
            
            $html = '<div data-page-group data-format="'.$this->params['listSize'].'" '.
                'data-orientation="'.$this->params['listOrientation'].'" '.
                'data-margin-left="'.self::PDF_MARGIN.'" data-margin-right="'.self::PDF_MARGIN.'" '.
                'data-margin-top="'.self::PDF_MARGIN_TOP.'" data-margin-bottom="'.self::PDF_MARGIN_BOTTOM.'" '.
                'data-header-top="'.self::PDF_MARGIN_HEADER.'" data-footer-bottom="'.self::PDF_MARGIN_FOOTER.'"></div>';
            $html .= '<div style="font-family: \'Roboto\'; font-weight: 400; font-style: normal; font-size: '.$this->fontSize.'px">';
            return $html;
            
        }
        
        /**
         * htmlPageClose -  End landscape/portrait page
         *
         * @access protected
         * @return string HTML code
         */
        protected function htmlPageClose(): string
        {
            
            $html = '</div></div>';
            return $html;
            
        }
    
        /**
         * htmlHeader - Set page header
         *
         * @access protected
         * @param  string $projectTitle Project title
         * @param  string $moduleTitle Module title
         * @return string HTML code
         */
        protected function htmlHeader(string $projectTitle, string $moduleTitle): string
        {
            
            $html = '<div data-header>'.
                '<div style="padding: '.self::PDF_MARGIN.'px; height: '.self::PDF_HEADER_HEIGHT.'px">'.
                '<table class="header" style="width: 100%"><tbody>'.
                '  <tr>'.
                '    <td style="width:30%; vertical-align: top;">'.
                '      <img src="'.$this->config->printLogoURL.'" '.
                '        style="height: '.self::PDF_HEADER_ROW_HEIGHT.'px" /></td>'.
                '    <td style="width: 40%; text-align: center; font-size: '.$this->fontSizeTitle.'px">'.
                '      <strong>'.$moduleTitle.'</strong></td>'.
                '    <td style="width: 30%; text-align: right; vertical-align:top;">'.
                '      <strong>'.$projectTitle.'</strong><br /><em>'.date($this->language->__('pdf.language.longdateformat')).'</em></td>'.
                '  </tr>'.
                '</tbody></table>'.
                '</div></div>';
            return $html;
            
        }

        /**
         * htmlFooter - Set page footer
         *
         * @access protected
         * @param  string $templateName Template name
         * @param  string $displayer    Optional: Template disclaimer
         * @return string HTML code
         */
        protected function htmlFooter(string $templateName, string $disclaimer = ''): string
        {
            
            $html = '<div data-footer>'.
                '<div style="padding-top: 0; padding-left: '.self::PDF_MARGIN.'px; padding-right: '.self::PDF_MARGIN.'px; '.
                '  height: '.self::PDF_FOOTER_HEIGHT.'px; font-size: '.$this->fontSizeSmall.'px;">'.
                '<table class="footer" style="width: 100%;"><tbody>'.
                '  <tr>'.
                '    <td style="text-align:left;width:70%;vertical-align:top;"><strong>'.$this->language->__($templateName).'</strong>'.
                '</td>'.
                '    <td style="text-align: right; width: 30%; vertical-align:top">'.$this->language->__('pdf.label.page').' {p}</td>'.
                '  </tr>'.
                '</tbody></table>'.
                (!empty($disclaimer) ? '<p>'.$this->language->__($disclaimer).'</p>' : '').
                '</div></div>';
            return $html;

        }

        /**
         * htmlStyles - Generate HML code for supported stypes
         *
         * @access protected
         * @return string HTML code
         */
        protected function htmlStyles(): string
        {

            $html = '<style>'.
                '  .header { border-collapse: collapse; }'.
                '  .footer { border-collapse: collapse; }'.
                '  .canvas { border-collapse: collapse; }'.
                '  .canvas-elt-title { background: '.self::PDF_COLOR_BG_SUBTITLE.';text-align:center;padding:5px;border:2px solid white; }'.
                '  .canvas-elt-box { background: '.self::PDF_COLOR_BG.'; vertical-align: top; text-align: left; '.
                '     padding: 2px; border: 2px solid white; }'.
                '  .canvas-box { vertical-align:top; text-align:left; padding: 0px 1px 0px 1px;}'.
                '  .list-title { font-weight: bold; width: 100%; color: white; background: darkgrey; padding: 4px; }'.
                '  .list-elt-title { font-weight: bold; width: 100%; background: '.self::PDF_COLOR_BG_SUBTITLE.'; padding: 4px;}'.
                '  .list-elt-box { vertical-align: top; width: 100%; padding: 4px 0px 4px 0px; }'.
                '  .hr-black { background: black; border: .5px; }'.
                '</style>';
            return $html;
            
        }

        /**
         * htmlIcon - Type set specific icon from fontawsome font 
         *
         * @access protected
         * @param  string $icon     FontAwesome name of icon
         * @param  int    $fontSize Optional: Font size, or 0 if not font size adjustment
         * @return string HTML code
         */
        protected function htmlIcon(string $icon, int $fontSize = 0): string
        {
            
            $iconCode = match($icon) {
				// Observe / Learn - Insights
                'fa-tower-observation' => '&#xe586',
				'fa-people-arrows' => '&#xe068',
				'fa-people-line' => '&#xe534',
				'fa-book' => '&#xf02d',
				'fa-file-signature' => '&#xf573',
				// SWOT Analysis
				'fa-dumbbell' => '&#xf44b',
				'fa-fire' => '&#xf06d',
                'fa-clover' => '&#xe139',
                'fa-bolt-lightning' => '&#xe0b7',
				// Empathy Map
				'fa-1' => '&#x0031',
				'fa-2' => '&#x0032',
				'fa-3' => '&#x0033',
				'fa-4' => '&#x0034',
				'fa-5' => '&#x0035',
				'fa-6' => '&#x0036',
				'fa-7' => '&#x0037',
				'fa-face-frown' => '&#xf119',
				'fa-face-smile' => '&#xf118',
				'fa-face-rolling-eyes' => '&#xf5a5',
				'fa-bullseye' => '&#xf140',
                // Strategy Brief
                'fa-list-check' => '&#xf0ae',
                'fa-heading' => '&#xf1dc',
                'fa-industry' => '&#xf275',
                'fa-file-lines' => '&#xf15c',
                'fa-people-group' => '&#xe533',
                'fa-sitemap' => '&#xf0e8',
                'fa-chalkboard-user' => '&#xf51c',
                'fa-person-digging' => '&#xf85e',
                'fa-money-bills' => '&#xe1f3',
                'fa-business-time' => '&#xf64a',
				'fa-masks-theater' => '&#xf630',
				'fa-book-skull' => '&#xf6b7',
				'fa-ruler-combined' => '&#xf546',
                'fa-person-falling' => '&#xe546',
				'fa-person-circle-question' => '&#xe542',
				'fa-person-circle-check' => '&#xe55c',
				'fa-person-circle-xmark' => '&#xe543',
				// Risk analysis
				'fa-tree' => '&#xf1bb',
				'fa-city' => '&#xf64f',
                'fa-person-skating' => '&#xf7c5',
                // Environmental analysis
                'fa-landmark' => '&#xf66f',
                'fa-chart-line' => '&#xf201',
                'fa-people-arrows' => '&#xe068',
                'fa-computer' => '&#xe4e5',
                'fa-scale-balanced' => '&#xf24e',
                'fa-cloud-sun' => '&#xf6c4',
				'fa-tower-observation' => '&#xe586',
				'fa-cloud-bolt' => '&#xf76c',
				'fa-arrow-trend-up' => '&#xe098',
                // Business model canvas
                'fa-users' => '&#xf0c0',
                'fa-barcode' => '&#xf02a',
                'fa-pen-ruler' => '&#xf5ae',
                'fa-money-bill' => '&#xf0d6',
                'fa-pen-to-square' => '&#xf044',
                'fa-user-doctor' => '&#xf0f0',
                'fa-heart' => '&#xf004',
                'fa-truck' => '&#xf0d1',
                'fa-money-bill-transfer' => '&#xe528',
                'fa-chess' => '&#xf439',
                'fa-hand-holding-dollar' => '&#xf4c0',
                'fa-handshake' => '&#xf2b5',
                'fa-apple-whole' => '&#xf5d1',
                'fa-industry' => '&#xf275',
                'fa-person-digging' => '&#xf85e',
                'fa-lightbulb' => '&#xf0eb',
                'fa-sack-dollar' => '&#xf81d',
                // Strategic Questions
                'fa-clipboard-question', => '&#xe4e3',
                // Strategic Message
                'fa-envelope-open', => '&#xf2b6',
                // Generic
                'fa-tags' => '&#xf02c',
                'fa-question' => '&#x003f',
                'fa-xmark' => '&#xf00d',
                'fa-check' => '&#xf00c',
                'fa-stop' => '&#xf04d',
                'fa-circle-question' => '&#xf059',
				'fa-circle-exclamation' => '&#xf06a',
                'fa-circle-xmark' => '&#xf057',
                'fa-circle-h' => '&#xf47e',
                'fa-circle-check' => '&#xf058',
                default => ''
            };
            $fontSize = ($fontSize === 0 ? $this->fontSize : $fontSize);
            $html = '<span style="font-family: \'FontAwesome\'; font-weight: 900; font-style: normal; font-size: '.$fontSize.'px;">'.
                $iconCode.'</span>';
            return $html;
            
        }

        /**
         * htmlCanvasTitle - Typeset title of element box in canvas
         *
         * @access protected
         * @param  string $text Canvas element title
         * @param  string $icon Optional: Icon associated with canvas element (FontAwesome code)
         * @return string HTML code
         */
        protected function htmlCanvasTitle(string $text, string $icon = ''): string
        {
            
            return (!empty($icon) ? $this->htmlIcon($icon).' ' : '').'<strong>'.$this->language->__($text).'</strong>';

        }
    
        /**
         * htmlCanvasElements - Typeset data of element box in canvas 
         *
         * @access protected
         * @param  array  $recordsAry Array of canvas data records
         * @para,  string $box        Identifier of elements/box to display
         * @return string HTML code
         */
        protected function htmlCanvasElements(array $recordsAry, string $box): string
        {
            
            $html = '<table class="table" style="width: 100%"><tbody>';
            foreach($recordsAry as $record) {
				$filterStatus = $this->filter['status'] ?? 'all';
				$filterRelates = $this->filter['relates'] ?? 'all';
                if($record['box'] === $box && ($filterStatus == 'all' || (!empty($statusLabels) && $filterStatus == $record['status'])) && 
				   ($filterRelates == 'all' || (!empty($relatesLabels) && $filterRelates == $record['relates']))) {
                    $html .= '<tr><td style="width: 14px;" class="canvas-box">'.$this->htmlIcon('fa-stop').'</td>'.
                        '  <td class="canvas-box"><span style="font-family: \'RobotoCondensed\';">'.$record['description'].'</span> '.
                        (!empty($this->statusLabels) ? $this->htmlCanvasStatus($record['status']) : '').'</td></tr>';
                }
            }
            $html .= '</tbody></table>';
            return $html;
            
        }

        /**
         * htmlListBoxTitle -  Typeset title of element box in list view
         *
         * @access protected
         * @param  string $text Canvas element title
         * @param  string $icon Optional: Icon associated with canvas element (FontAwesome code)
         * @return string HTML code
         */
        protected function htmlListTitle(string $text, string $icon = ''): string
        {
            
            return '<div class="list-title" style="font-size: '.$this->fontSizeLarge.'px">'.
                (!empty($icon) ? $this->htmlIcon($icon).' ' : '').'<strong>'.$this->language->__($text).'</strong></div>';

        }
    
        /**
         * htmlListElementsDetailed - Typeset data of element box in canvas 
         *
         * @access protected
         * @param  array  $recordsAry Array of canvas data records
         * @para,  string $box        Identifier of elements/box to display
         * @return string HTML code
         */
        protected function htmlListElementsDetailed(array $recordsAry, string $box): string
        {
            
            $html = '';
            foreach($recordsAry as $record) {
				$filterStatus = $this->filter['status'] ?? 'all';
				$filterRelates = $this->filter['relates'] ?? 'all';
                if($record['box'] === $box && ($filterStatus == 'all' || $filterStatus == $record['status']) && 
				   ($filterRelates == 'all' || $filterRelates == $record['relates'])) {

                    if(isset($record['description']) && !empty($record['description'])) {
                        $html .= '<div class="list-elt-box"><strong>'.$record['description'].'</strong></div>';
                    }
					
                    if(isset($record['relates']) && !empty($record['relates'])) {
                        $relates = $this->htmlListRelates($record['relates']);
                        if(!empty($relates)) {
                            $html .= '<div class="list-elt-box">'.$this->language->__($this->params['elementRelates']).': '.
                                '<em>'.$relates.'</em></div>';
                        }
                    }
					
                    if(isset($record['status']) && !empty($record['status'])) {
                        $status = $this->htmlListStatus($record['status']);
                        if(!empty($status)) {
                            $html .= '<div class="list-elt-box">'.$this->language->__($this->params['elementStatus']).': '.
                                '<em>'.$status.'</em></div>';
                        }
                    }
					
                    if($this->dataLabels[1]['active'] && isset($record[$this->dataLabels[1]['field']]) && 
                        !empty($record[$this->dataLabels[1]['field']])) {
                        $html .= '<div class="list-elt-title">'.$this->dataLabels[1]['title'].'</div>';
                        $html .= '<div class="list-elt-box">'.$this->htmlStripTags($record[$this->dataLabels[1]['field']]).'</div>';
                    }
							
                    if($this->dataLabels[2]['active'] && isset($record[$this->dataLabels[2]['field']]) && 
                        !empty($record[$this->dataLabels[2]['field']])) {
                        $html .= '<div class="list-elt-title">'.$this->dataLabels[2]['title'].'</div>';
                        $html .= '<div class="list-elt-box">'.$this->htmlStripTags($record[$this->dataLabels[2]['field']]).'</div>';
                    }
							
                    if($this->dataLabels[3]['active'] && isset($record[$this->dataLabels[3]['field']]) && 
                        !empty($record[$this->dataLabels[3]['field']])) {
                        $html .= '<div class="list-elt-title">'.$this->dataLabels[3]['title'].'</div>';
                        $html .= '<div class="list-elt-box">'.$this->htmlStripTags($record[$this->dataLabels[3]['field']]).'</div>';
                    }
                    $html .= '<hr class="hr-black"/>';
                }
            }
            return $html;
            
        }

        /**
         * htmlListElementsCompact - Typeset data of element box in canvas in short form
         *
         * @access protected
         * @param  array  $recordsAry Array of canvas data records
         * @para,  string $box        Identifier of elements/box to display
         * @return string HTML code
         */
        protected function htmlListElementsCompact(array $recordsAry, string $box): string
        {
            
            $html = '';
            foreach($recordsAry as $record) {
				$filterStatus = $this->filter['status'] ?? 'all';
				$filterRelates = $this->filter['relates'] ?? 'all';
                if($record['box'] === $box && ($filterStatus == 'all' || $filterStatus == $record['status']) && 
				   ($filterRelates == 'all' || $filterRelates == $record['relates'])) {

                    $html .= '<div style="margin-top: 5px; margin-bottom: 5px;">';
                    if(isset($record['description']) && !empty($record['description'])) {
                        $html .= '<strong>'.$record['description'].'</strong>';
                    }

                    if($this->dataLabels[1]['active'] && !empty($record[$this->dataLabels[1]['field']]) && 
                        isset($record['description']) && !empty($record['description'])) {
						$html .= ' - ';
					}

                    if($this->dataLabels[1]['active'] && !empty($record[$this->dataLabels[1]['field']])) {
                        $html .= $this->htmlStripTags($record[$this->dataLabels[1]['field']]);
					}
					
                    if((isset($record['status']) && !empty($record['status'])) ||
					   (isset($record['relates']) && !empty($record['relates']))) {
						$html .= ' (';
					}

                    if(isset($record['status']) && !empty($record['status'])) {
                        $status = $this->htmlListStatus($record['status']);
                        if(!empty($status)) {
                            $html .= $this->language->__($status);
                        }
                    }
					
                    if((isset($record['status']) && !empty($record['status'])) &&
					   (isset($record['relates']) && !empty($record['relates']))) {
						$html .= ', ';
					}

                    if(isset($record['relates']) && !empty($record['relates'])) {
                        $relates = $this->htmlListRelates($record['relates']);
                        if(!empty($relates)) {
                            $html .= $this->language->__($relates);
                        }
                    }
					
                    if((isset($record['status']) && !empty($record['status'])) ||
					   (isset($record['relates']) && !empty($record['relates']))) {
						$html .= ')';
					}
                    $html .= '<hr class="hr-black"/>';
					$html .= '</div>';
                }
            }
            return $html;
            
        }

        /**
         * htmlStripTags - Strip / replace tags that cannot be processed by YetiForcePDF
         *
         * @access protected
         * @param  string $html HTML code containing tags
         * @return string HMTL code
         */
        protected function htmlStripTags(string $html): string
        {

            if(substr($html, 0, 3) === '<p>') { $html = substr($html, 3); }
            if(substr($html, -4) === '</p>') { $html = substr($html, 0, strlen($html) - 4); }
            $html = str_replace('<p>', '<br>', str_replace('</p>', '', $html));
            $html = str_replace('<ul>', '<br>', $html);
            $html = str_replace('<ol>', '<br>', $html);
            $html = str_replace('<ul class="tox-checklist" style="list-style-type: none;">', '<br>', $html);
            $html = str_replace('<li>', $this->htmlIcon('fa-stop', $this->fontSizeSmall).' ', $html);
            $html = str_replace('</li>', '<br>', $html);
            $html = str_replace('</ul>', '', $html);
            $html = str_replace('</ol>', '', $html);
            return $html;

        }
    
    }
}