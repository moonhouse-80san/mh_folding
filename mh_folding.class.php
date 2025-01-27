<?php
	/**
	 * @class mh_folding
	 * @author zero (zero@nzeo.com)
	 * @brief 최근 게시물을 출력하는 위젯
	 * @version 0.1
	 **/

	class mh_folding extends WidgetHandler {

		/**
		 * @brief 위젯의 실행 부분
		 *
		 * ./widgets/위젯/conf/info.xml 에 선언한 extra_vars를 args로 받는다
		 * 결과를 만든후 print가 아니라 return 해주어야 한다
		 **/
			function proc($args) {

			//기본 객체 설정
			$oModuleModel = &getModel('module');
			$oDocumentModel = &getModel('document');
			Context::set('oModuleModel',$oModuleModel);
			Context::set('oDocumentModel',$oDocumentModel);

			if(!in_array($args->order_target, array('regdate', 'list_order', 'update_order'))) $args->order_target = 'list_order';
			if(!in_array($args->order_type, array('asc','desc'))) $args->order_type = 'desc';

			$list_count = (int)$args->list_count;
			if(!$list_count) $list_count = 5;
			$obj->list_count = $list_count;

			$args->cols_list_count = isset($args->cols_list_count) ? (int)$args->cols_list_count : 1;
			$args->m_leftmargin = isset($args->m_leftmargin) ? (int)$args->m_leftmargin : 0;
			$args->subject_cut_size = isset($args->subject_cut_size) ? (int)$args->subject_cut_size : 0;
			$args->content_cut_size = isset($args->content_cut_size) ? (int)$args->content_cut_size : 200;
			$args->content_l_h = isset($args->content_l_h) ? $args->content_l_h : '1.6';
			$args->thumbnail_type = isset($args->thumbnail_type) ? $args->thumbnail_type : 'fill';
			$args->thumbnail_width = isset($args->thumbnail_width) ? (int)$args->thumbnail_width : 100;
			$args->thumbnail_height = isset($args->thumbnail_height) ? (int)$args->thumbnail_height : 100;
			$args->title_size = isset($args->title_size) ? $args->title_size : '1.6';
			$args->sum_font_size = isset($args->sum_font_size) ? $args->sum_font_size : '1';
			$args->border_size = isset($args->border_size) ? (int)$args->border_size : 2;
			$args->border_color = isset($args->border_color) ? $args->border_color : '#000';
			$args->title_color = isset($args->title_color) ? $args->title_color : '#eee';
			$args->background_color = isset($args->background_color) ? $args->background_color : 'transparent';
			$args->sum_font_color = isset($args->sum_font_color) ? $args->sum_font_color : '#eee';
			$args->m_left = isset($args->m_left) ? (int)$args->m_left : 100;
			$args->back_color1 = isset($args->back_color1) ? $args->back_color1 : '#5b927d';
			$args->back_color2 = isset($args->back_color2) ? $args->back_color2 : '#a8ae7e';
			$args->back_color3 = isset($args->back_color3) ? $args->back_color3 : '#f3b96c';
			$args->back_color4 = isset($args->back_color4) ? $args->back_color4 : '#835531';
			$args->back_color5 = isset($args->back_color5) ? $args->back_color5 : '#9C4998';
			$args->box_width = isset($args->box_width) ? (int)$args->box_width : 200;
			$args->box_height = isset($args->box_height) ? (int)$args->box_height : 300;
			$args->b_name_color = isset($args->b_name_color) ? $args->b_name_color : '#444';
			$args->bn_lo = isset($args->bn_lo) ? $args->bn_lo : '10px 0 0 -44%';
			$args->c_lo = isset($args->c_lo) ? $args->c_lo : '0 0 0 45%';
			$args->thumbnail_zoom = isset($args->thumbnail_zoom) ? (int)$args->thumbnail_zoom : 5;
			$args->w_width = isset($args->w_width) ? $args->w_width : '100%';

			// LightBox 설정
			$args->lr_duration = isset($args->lr_duration) ? (int)$args->lr_duration : 700;
			$args->li_duration = isset($args->li_duration) ? (int)$args->li_duration : 600;
			$args->l_top = isset($args->l_top) ? (int)$args->l_top : 50;
			$l_scrolling = $args->l_scrolling;
			$lt_nav = $args->lt_nav;
			$l_around = $args->l_around;

			// 노출 여부 체크
			$b_img = $args->b_img;
			$b_name = $args->b_name;
			$zoom_s = $args->zoom_s;
			$view_type = $args->view_type;
			$boxshadow = $args->boxshadow;
			$thumb_gray = $args->thumb_gray;
			$widget_close = $args->widget_close;
			$title = $args->title;
			$display_summary = $args->display_summary;
			$thumd_nails = $args->thumd_nails;
			$display_author = $args->display_author;
			$display_regdate = $args->display_regdate;
			$more = $args->more;
			$ajax_use = $args->ajax_use;
			$animate_use = $args->animate_use;
			$name_use = $args->name_use;

			// Direct
			$faccordion_1 = $args->faccordion_1;
			$faccordion_1_url = $args->faccordion_1_url;
			$faccordion_1_text = $args->faccordion_1_text;
			$faccordion_1_sum = $args->faccordion_1_sum;

			$faccordion_2 = $args->faccordion_2;
			$faccordion_2_url = $args->faccordion_2_url;
			$faccordion_2_text = $args->faccordion_2_text;
			$faccordion_2_sum = $args->faccordion_2_sum;

			$faccordion_3 = $args->faccordion_3;
			$faccordion_3_url = $args->faccordion_3_url;
			$faccordion_3_text = $args->faccordion_3_text;
			$faccordion_3_sum = $args->faccordion_3_sum;

			$faccordion_4 = $args->faccordion_4;
			$faccordion_4_url = $args->faccordion_4_url;
			$faccordion_4_text = $args->faccordion_4_text;
			$faccordion_4_sum = $args->faccordion_4_sum;

			$faccordion_5 = $args->faccordion_5;
			$faccordion_5_url = $args->faccordion_5_url;
			$faccordion_5_text = $args->faccordion_5_text;
			$faccordion_5_sum = $args->faccordion_5_sum;

			$faccordion_6 = $args->faccordion_6;
			$faccordion_6_url = $args->faccordion_6_url;
			$faccordion_6_text = $args->faccordion_6_text;
			$faccordion_6_sum = $args->faccordion_6_sum;

			// 대상 모듈
			$mid_list = explode(",",$args->mid_list);

			// module_srl 대신 mid가 넘어왔을 경우는 직접 module_srl을 구해줌
			if($mid_list) {
				$oModuleModel = &getModel('module');
				$module_srl = $oModuleModel->getModuleSrlByMid($mid_list);
			}

			// 대상 모듈 (mid_list는 기존 위젯의 호환을 위해서 처리하는 루틴을 유지. module_srl로 위젯에서 변경)
			if($args->mid_list) {
				$mid_list = explode(",",$args->mid_list);
				$oModuleModel = &getModel('module');
				if(count($mid_list)) {
					$module_srl = $oModuleModel->getModuleSrlByMid($mid_list);
				} else {
					$site_module_info = Context::get('site_module_info');
					if($site_module_info) {
						$margs->site_srl = $site_module_info->site_srl;
						$oModuleModel = &getModel('module');
						$output = $oModuleModel->getMidList($margs);
						if(count($output)) $mid_list = array_keys($output);
						$module_srl = $oModuleModel->getModuleSrlByMid($mid_list);
					}
				}
			} else $module_srl = explode(',',$args->module_srls);

			// DocumentModel::getDocumentList()를 이용하기 위한 변수 정리
			if(is_array($module_srl)) $obj->module_srl = implode(',',$module_srl);
			else $obj->module_srl = $module_srl;
			$obj->sort_index = $order_target;
			if($args->order_target == 'list_order' || $args->order_target == 'update_order')
			{
				$obj->order_type = $args->order_type=="desc"?"asc":"desc";
			}
			else
			{
				$obj->order_type = $args->order_type=="desc"?"desc":"asc";
			}

			if($obj->sort_index == 'list_order') $obj->avoid_notice = -2100000000;

			/* 페이지 네비게이션 기능 추가 */
			// 페이지 목록 수 (1,2,3,4....)
			$page_count = $args->page_count;
			if(!$page_count) $page_count = 5;
			$obj->page_count = $page_count;
			// 페이지 출력 여부
			$page_type = $args->page_type;
			// 페이지 번호 구하기
			if($page_type=='Y') $obj->page = (Context::get('page'))? Context::get('page'): 1;
			/* 페이지 네비게이션 기능 추가 끝 */

			$obj->category_srl = $args->category_srl;
			$output = executeQueryArray('widgets.mh_folding.getMhMulti', $obj);
			$output_count = executeQueryArray('widgets.mh_folding.getNewestDocumentsCount', $obj);

			// document 모듈의 model 객체를 받아서 결과를 객체화 시킴
			$oDocumentModel = &getModel('document');

			// 오류가 생기면 그냥 무시
			if(!$output->toBool()) return;

			// 결과가 있으면 각 문서 객체화를 시킴
			if($order_type == "RAND") shuffle($output->data);
			if(count($output->data)) {
				foreach($output->data as $key => $attribute) {
					$document_srl = $attribute->document_srl;

					$oDocument = null;
					$oDocument = new documentItem();
					$oDocument->setAttribute($attribute);

					if($args->view_category == "on") {
						$oDocument->category = @$oDocumentModel->getCategory($attribute->category_srl);
						$oDocument->category_srl = $attribute->category_srl;
					}

					$oModuleInfo = $oModuleModel->getModuleInfoByModuleSrl($attribute->module_srl);
					$oDocument->mid = $oModuleInfo->mid;

					// 확장 변수 출력
					if($args->extra_var_num) {
						$oDocument->extra_value = $oDocument->getExtraValue($args->extra_var_num);
						$oDocument->extra_var_num = $args->extra_var_num;
					}

					// 모듈이 여러개일 때 메뉴 명 구함
					if((count($mid_list) > 1 || !$args->mid_list) && $args->view_menuname == "on") {
						// 탭 이름 설정
						switch($args->menuname) {
							case "browser_title" : $oDocument->menuname = $oModuleInfo->browser_title; break;
							case "title" : $oDocument->menuname = $oModuleInfo->title; break;

						}
					}

					$document_list[$key] = $oDocument;

					/* 출력되는 모듈의 번호를 구함 */
					$document_modulies[$attribute->module_srl] = $attribute->module_srl;
				}
			} else {
				$document_list = array();
			}

			// 템플릿 파일에서 사용할 변수들을 세팅
			if(count($mid_list)==1) $args->module_name = $mid_list[0];

			$args->document_list = $document_list;

			/* 페이지 네비게이션 기능 추가 */
			$args->total_count = $output_count->data[0]->count; // 전체 개시물 수
			$args->page = (!Context::get('page'))? 1:Context::get('page'); // 현재 페이지
			// 총 페이지 수
			$total_count = $args->total_count/$list_count;
			if($args->total_count%$list_count != 0) $total_count+=1;
			$args->total_page = floor($total_count); // 총 페이지 수
			$args->page_count = $page_count; // 총 페이지 수
			/* 페이지 네비게이션 기능 추가 끝 */

			if($args->module_srls){
				$obj->module_srl = $args->module_srls;
				$output = executeQueryArray('document.getDocumentList', $obj);
				Context::set('box_list',$output->data);
			}

			Context::set('widget_info', $args);

			// 템플릿의 스킨 경로를 지정 (skin, colorset에 따른 값을 설정)
			$tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
			Context::set('colorset', $args->colorset);

			// 템플릿 파일을 지정
			$tpl_file = 'list';

			// 템플릿 컴파일
			$oTemplate = &TemplateHandler::getInstance();
			$output = $oTemplate->compile($tpl_path, $tpl_file);
			return $output;
		}
	}

	 /**
	 * @brief 다음 페이지 요청
	 **/
	function WD_getNextPage_mf($total_count, $total_page, $cur_page, $page_count = 10) {

		$first_page = $cur_page - (int)($page_count/2);
		if($first_page<1) $first_page = 1;
		$last_page = $total_page;
		if($last_page>$total_page) $last_page = $total_page;
		if($total_page < $page_count) $page_count = $total_page;

		$GLOBALS['wd_total_page'] = $total_page;
		$GLOBALS['wd_first_page'] = $first_page;
		$GLOBALS['wd_page_count'] = $page_count;
		$GLOBALS['wd_last_page'] = $last_page;

		$page->first_page = $first_page;
		$page->last_page = $last_page;

		return $page;
	}

	function WD_getNextPage_mf2() {
		$page = $GLOBALS['wd_first_page']+$GLOBALS['wd_i']++;
		if($GLOBALS['wd_i'] > $GLOBALS['wd_page_count'] || $page > $GLOBALS['wd_last_page']) $page = 0;
		return $page;
	}

	function WD_getNextClear_mf() {
		$GLOBALS['wd_i'] = 0;
	}
?>