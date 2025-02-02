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

		// 기본 객체 설정
		$oModuleModel = getModel('module');
		$oDocumentModel = getModel('document');
		Context::set('oModuleModel', $oModuleModel);
		Context::set('oDocumentModel', $oDocumentModel);

		// 정렬 대상
		$valid_order_targets = ['list_order', 'update_order', 'regdate', 'rand()', 'voted_count', 'readed_count', 'comment_count'];
		$args->order_target = in_array($args->order_target ?? '', $valid_order_targets) ? $args->order_target : 'list_order';

		$obj = new stdClass();
		$obj->sort_index = $args->order_target;

		// 정렬 순서
		$order_type = $args->order_type ?? 'asc';
		if (!in_array($order_type, ['asc', 'desc'])) $order_type = 'asc';
		$obj->order_type = $order_type == "desc" ? "asc" : "desc";

		// 총 목록 수
		$list_count = (int)($args->list_count ?? 5);
		if (!$list_count) $list_count = 5;
		$obj->list_count = $list_count;

		// 설정값들의 기본값 처리
		$defaults = [
			'subject_cut_size' => 0,
			'content_cut_size' => 200,
			'content_l_h' => '1.6',
			'thumbnail_type' => 'fill',
			'thumbnail_width' => 100,
			'thumbnail_height' => 100,
			'title_size' => '1.6',
			'sum_font_size' => '1',
			'border_size' => 2,
			'border_color' => '#000',
			'title_color' => '#eee',
			'background_color' => 'transparent',
			'sum_font_color' => '#eee',
			'm_left' => 100,
			'back_color1' => '#5b927d',
			'back_color2' => '#a8ae7e',
			'back_color3' => '#f3b96c',
			'back_color4' => '#835531',
			'back_color5' => '#9C4998',
			'box_width' => 200,
			'box_height' => 300,
			'b_name_color' => '#444',
			'bn_lo' => '10px 0 0 -44%',
			'c_lo' => '0 0 0 45%',
			'thumbnail_zoom' => 5,
			'w_width' => '100%',
			// LightBox 설정
			'lr_duration' => 700,
			'li_duration' => 600,
			'l_top' => 50,
			'l_scrolling' => null,
			'lt_nav' => null,
			'l_around' => null,
			// 노출 여부 체크
			'b_img' => null,
			'b_name' => null,
			'zoom_s' => null,
			'view_type' => null,
			'boxshadow' => null,
			'thumb_gray' => null,
			'widget_close' => null,
			'title' => null,
			'display_summary' => null,
			'thumd_nails' => null,
			'display_author' => null,
			'display_regdate' => null,
			'more' => null,
			'ajax_use' => null,
			'animate_use' => null,
			'name_use' => null
		];

		// Direct
		for ($i = 1; $i <= 6; $i++) {
			Context::set("faccordion_{$i}", $args->{"faccordion_{$i}"} ?? '');
			Context::set("faccordion_{$i}_url", $args->{"faccordion_{$i}_url"} ?? '');
			Context::set("faccordion_{$i}_text", $args->{"faccordion_{$i}_text"} ?? '');
			Context::set("faccordion_{$i}_sum", $args->{"faccordion_{$i}_sum"} ?? '');
		}

		// 대상 모듈
		$mid_list = explode(",", $args->mid_list ?? '');

		// module_srl 대신 mid가 넘어왔을 경우는 직접 module_srl을 구해줌
		if ($mid_list) {
			$oModuleModel = getModel('module');
			$module_srl = $oModuleModel->getModuleSrlByMid($mid_list);
		}

		// 대상 모듈 (mid_list는 기존 위젯의 호환을 위해서 처리하는 루틴을 유지. module_srl로 위젯에서 변경)
		if ($args->mid_list) {
			$mid_list = explode(",", $args->mid_list);
			$oModuleModel = getModel('module');
			if (count($mid_list)) {
				$module_srl = $oModuleModel->getModuleSrlByMid($mid_list);
			} else {
				$site_module_info = Context::get('site_module_info');
				if ($site_module_info) {
					$margs = new stdClass();
					$margs->site_srl = $site_module_info->site_srl;
					$oModuleModel = getModel('module');
					$output = $oModuleModel->getMidList($margs);
					if (count($output)) $mid_list = array_keys($output);
					$module_srl = $oModuleModel->getModuleSrlByMid($mid_list);
				}
			}
		} else {
			$module_srl = explode(',', $args->module_srls ?? '');
		}

		// DocumentModel::getDocumentList()를 이용하기 위한 변수 정리
		if (is_array($module_srl)) $obj->module_srl = implode(',', $module_srl);
		else $obj->module_srl = $module_srl;
		$obj->sort_index = $args->order_target;
		if ($args->order_target == 'list_order' || $args->order_target == 'update_order') {
			$obj->order_type = $args->order_type == "desc" ? "asc" : "desc";
		} else {
			$obj->order_type = $args->order_type == "desc" ? "desc" : "asc";
		}

		if ($obj->sort_index == 'list_order') $obj->avoid_notice = -2100000000;

		/* 페이지 네비게이션 기능 추가 */
		// 페이지 목록 수 (1,2,3,4....)
		$page_count = $args->page_count ?? 5;
		if (!$page_count) $page_count = 5;
		$obj->page_count = $page_count;
		// 페이지 출력 여부
		$page_type = $args->page_type ?? null;

		// 페이지 번호 구하기 
		if ($page_type == 'Y') {
			// 현재 페이지의 mid와 widget_srl을 조합하여 유니크한 세션 키 생성
			$current_mid = Context::get('mid');
			$session_key = 'mh_folding_' . $current_mid . '_' . $args->widget_srl . '_page';
			$current_page = (int)(Context::get('page'));
			if (!$current_page) {
				$current_page = (int)($_SESSION[$session_key] ?? 1);
			}
			if ($current_page < 1) $current_page = 1;
			// 현재 페이지 저장 (mid와 widget_srl 조합으로)
			$_SESSION[$session_key] = $current_page;
			$obj->page = $current_page;
			// 페이지네이션 처리
			$obj->list_count = $list_count;
			$obj->start_count = ($current_page - 1) * $list_count;
			Context::set('page', $current_page);
		}
		/* 페이지 네비게이션 기능 추가 끝 */

		$obj->category_srl = $args->category_srl ?? null;
		$output = executeQueryArray('widgets.mh_folding.getMhMulti', $obj);
		$output_count = executeQueryArray('widgets.mh_folding.getNewestDocumentsCount', $obj);

		// document 모듈의 model 객체를 받아서 결과를 객체화 시킴
		$oDocumentModel = getModel('document');

		// 오류가 생기면 그냥 무시
		if (!$output->toBool()) return;

		// 결과가 있으면 각 문서 객체화를 시킴
		if ($order_type == "RAND") shuffle($output->data);
		if (count($output->data)) {
			foreach ($output->data as $key => $attribute) {
				$document_srl = $attribute->document_srl;

				$oDocument = new documentItem();
				$oDocument->setAttribute($attribute);

				if ($args->view_category == "on") {
					$oDocument->category = @$oDocumentModel->getCategory($attribute->category_srl);
					$oDocument->category_srl = $attribute->category_srl;
				}

				$oModuleInfo = $oModuleModel->getModuleInfoByModuleSrl($attribute->module_srl);
				$oDocument->mid = $oModuleInfo->mid;

				// 확장 변수 출력
				if ($args->extra_var_num) {
					$oDocument->extra_value = $oDocument->getExtraValue($args->extra_var_num);
					$oDocument->extra_var_num = $args->extra_var_num;
				}

				// 모듈이 여러개일 때 메뉴 명 구함
				if ((count($mid_list) > 1 || !$args->mid_list) && $args->view_menuname == "on") {
					// 탭 이름 설정
					switch ($args->menuname) {
						case "browser_title":
							$oDocument->menuname = $oModuleInfo->browser_title;
							break;
						case "title":
							$oDocument->menuname = $oModuleInfo->title;
							break;
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
		if (count($mid_list) == 1) $args->module_name = $mid_list[0];

		$args->document_list = $document_list;

		// 페이지 네비게이션 설정
		if (isset($output_count)) {
			$args->total_count = $output_count->data[0]->count ?? 0;
			$args->page = Context::get('page') ?: 1;
			$total_count = $args->total_count / $list_count;
			if ($args->total_count % $list_count != 0) $total_count += 1;
			$args->total_page = floor($total_count);
			$args->page_count = $args->page_count ?? 5;
		}

		if ($args->module_srls) {
			$obj->module_srl = $args->module_srls;
			$output = executeQueryArray('document.getDocumentList', $obj);
			Context::set('box_list', $output->data);
		}

		Context::set('widget_info', $args);

		// 템플릿의 스킨 경로를 지정 (skin, colorset에 따른 값을 설정)
		$tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
		Context::set('colorset', $args->colorset);

		// 템플릿 파일을 지정
		$tpl_file = 'list';

		// 템플릿 컴파일
		$oTemplate = TemplateHandler::getInstance();
		$output = $oTemplate->compile($tpl_path, $tpl_file);
		return $output;
	}
}

/**
 * @brief 다음 페이지 요청
 **/
function WD_getNextPage_mf($total_count, $total_page, $cur_page, $page_count = 10) {
	$first_page = $cur_page - (int)($page_count / 2);
	if ($first_page < 1) $first_page = 1;
	$last_page = $total_page;
	if ($last_page > $total_page) $last_page = $total_page;
	if ($total_page < $page_count) $page_count = $total_page;

	$GLOBALS['wd_total_page'] = $total_page;
	$GLOBALS['wd_first_page'] = $first_page;
	$GLOBALS['wd_page_count'] = $page_count;
	$GLOBALS['wd_last_page'] = $last_page;

	$page = new stdClass();
	$page->first_page = $first_page;
	$page->last_page = $last_page;

	return $page;
}

function WD_getNextPage_mf2() {
	$GLOBALS['wd_i'] = $GLOBALS['wd_i'] ?? 0;
	$page = $GLOBALS['wd_first_page'] + $GLOBALS['wd_i']++;
	if ($GLOBALS['wd_i'] > $GLOBALS['wd_page_count'] || $page > $GLOBALS['wd_last_page']) $page = 0;
	return $page;
}

function WD_getNextClear_mf() {
	$GLOBALS['wd_i'] = 0;
}