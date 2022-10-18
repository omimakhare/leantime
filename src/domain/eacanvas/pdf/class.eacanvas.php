<?php
/**
 * HTML code for PDF report
 */
namespace leantime\domain\pdf {
  
	use leantime\domain\repositories;
	
    class eacanvas extends \leantime\domain\pdf\canvas {

		protected const CANVAS_NAME = 'ea';
        
        /**
         * htmlCanvas -  Layout canvas (must be implemented)
         *
         * @access public
         * @param  array  $recordsAry Array of canvas data records
         * @return string HTML code
         */
        protected function htmlCanvas(array $recordsAry): string
        {
			
			$html = '<table class="canvas" style="width: 100%"><tbody><tr>';

			$html .= '<td class="canvas-elt-title" style="width: 33.33%;">'.
                $this->htmlCanvasTitle($this->canvasTypes['ea_political']['title'], $this->canvasTypes['ea_political']['icon']).'</td>';
			$html .= '<td class="canvas-elt-title" style="width: 33.33%;">'.
                $this->htmlCanvasTitle($this->canvasTypes['ea_economic']['title'], $this->canvasTypes['ea_economic']['icon']).'</td>';
			$html .= '<td class="canvas-elt-title" style="width: 33.33%;">'.
                $this->htmlCanvasTitle($this->canvasTypes['ea_societal']['title'], $this->canvasTypes['ea_societal']['icon']).'</td>';

			$html .= '</tr><tr>';

			$html .= '<td class="canvas-elt-box" style="height: 310px;">'.$this->htmlCanvasElements($recordsAry, 'ea_political').'</td>';
			$html .= '<td class="canvas-elt-box" style="height: 310px;">'.$this->htmlCanvasElements($recordsAry, 'ea_economic').'</td>';
			$html .= '<td class="canvas-elt-box" style="height: 310px;">'.$this->htmlCanvasElements($recordsAry, 'ea_societal').'</td>';

			$html .= '</tr><tr>';

			$html .= '<td class="canvas-elt-title" style="width: 33.33%;">'.
                $this->htmlCanvasTitle($this->canvasTypes['ea_technological']['title'], 
									   $this->canvasTypes['ea_technological']['icon']).'</td>';
			$html .= '<td class="canvas-elt-title" style="width: 33.33%;">'.
                $this->htmlCanvasTitle($this->canvasTypes['ea_legal']['title'], $this->canvasTypes['ea_legal']['icon']).'</td>';
			$html .= '<td class="canvas-elt-title" style="width: 33.33%;">'.
                $this->htmlCanvasTitle($this->canvasTypes['ea_ecological']['title'], $this->canvasTypes['ea_ecological']['icon']).'</td>';

			$html .= '</tr><tr>';

			$html .= '<td class="canvas-elt-box" style="height: 310px;">'.$this->htmlCanvasElements($recordsAry,'ea_technological').'</td>';
			$html .= '<td class="canvas-elt-box" style="height: 310px;">'.$this->htmlCanvasElements($recordsAry, 'ea_legal').'</td>';
			$html .= '<td class="canvas-elt-box" style="height: 310px;">'.$this->htmlCanvasElements($recordsAry, 'ea_ecological').'</td>';

			$html .= '</tr><tr>';

			$html .= '</tr></tbody></table>';

			return $html;

        }
        
        /***
         * reportGenerate - Generate report for module
         *
         * @access public
         * @param  int    $id     Canvas identifier
         * @param  string $filter Filter value
         * @return string PDF filename
         */
        public function reportGenerate(int $id, array $filter = []): string
        {

            // Retrieve canvas data
            $eaCanvasRepo = new repositories\eacanvas();
            $eaCanvasAry = $eaCanvasRepo->getSingleCanvas($id);
            !empty($eaCanvasAry) || die("Cannot find canvas with id '$id'");
            $projectId = $eaCanvasAry[0]['projectId'];
            $recordsAry = $eaCanvasRepo->getCanvasItemsById($id);
            $projectsRepo = new repositories\projects();
            $projectAry = $projectsRepo->getProject($projectId);
            !empty($projectAry) || die("Cannot retrieve project id '$projectId'");
            
            // Configuration
            $options = [ ];
            
            // Generate PDF content
            $pdf = new \YetiForcePDF\Document();
            $pdf->init();
            $pdf->loadHtml($this->htmlReport($projectAry['name'], $eaCanvasAry[0]['title'], $recordsAry, $filter, $options));
            return $pdf->render();

        }
    
    }
}
?>
