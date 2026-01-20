<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_dashboard_popup extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getColumn(){
		
		$id = $this->input->post('id',true);
		$tahun = $this->input->post('tahun',true);
		$provinsi_code = $this->input->post('provinsi_code',true);
		$kabkota_code = $this->input->post('kabkota_code',true);
		$kelompok_id = $this->input->post('kelompok_id',true);
		
		$column = array();
		
		// Kolom default untuk semua tipe drill down
		array_push($column, array(
			"data" => "no",
			"title" => "No"
		));
		
		array_push($column, array(
			"data" => "survei_pm_nama",
			"title" => "Nama Surveyor"
		));
		
		array_push($column, array(
			"data" => "survei_pm_email",
			"title" => "Email"
		));
		
		array_push($column, array(
			"data" => "survei_pm_tlp",
			"title" => "No. Telepon"
		));
		
		array_push($column, array(
			"data" => "wilayah",
			"title" => "Wilayah"
		));
		
		array_push($column, array(
			"data" => "kelompok",
			"title" => "Kelompok"
		));
		
		array_push($column, array(
			"data" => "nama_unit",
			"title" => "Nama Unit"
		));
		
		array_push($column, array(
			"data" => "tanggal",
			"title" => "Tanggal Survei"
		));
		
		array_push($column, array(
			"data" => "total_pria",
			"title" => "Pria"
		));
		
		array_push($column, array(
			"data" => "total_wanita",
			"title" => "Wanita"
		));
		
		array_push($column, array(
			"data" => "total_semua",
			"title" => "Total"
		));
		
		array_push($column, array(
			"data" => "status",
			"title" => "Status"
		));
		
		
		
		$data = array(
			'status' => 'success',
			'column' => $column,
			'csrf_hash' => $this->security->get_csrf_hash()
		);
		
		return json_encode($data);
	}
	
	public function getColumnDetail(){
		
		$id = $this->input->post('id',true);
		$tahun = $this->input->post('tahun',true);
		$provinsi_code = $this->input->post('provinsi_code',true);
		$kabkota_code = $this->input->post('kabkota_code',true);
		$kelompok_id = $this->input->post('kelompok_id',true);
		
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		
		$searchValue = $search['value'];
		
		// Base query
		$this->db->select('
			data_survei_pm.id,
			data_survei_pm.survei_pm_nama,
			data_survei_pm.survei_pm_email,
			data_survei_pm.survei_pm_tlp,
			CONCAT(m_set_wil_administratif.wil_prov_nama, \' - \', m_set_wil_administratif.wil_kab_nama, \' - \', 
			m_set_wil_administratif.wil_kec_nama) as wilayah,
			master_kelompok.nama_kelompok as kelompok,
			data_survei_pm_detail_list.nama_unit,
			TO_CHAR(data_survei_pm.created, \'DD-MM-YYYY HH24:MI\') as tanggal,
			CASE 
				WHEN data_survei_pm.status_id = 1 THEN \'Aktif\'
				WHEN data_survei_pm.status_id = 0 THEN \'Draft\'
				ELSE \'Lainnya\'
			END as status,
			SUM(data_survei_pm_detail_list.jumlah_pria)   as total_pria,
			SUM(data_survei_pm_detail_list.jumlah_wanita) as total_wanita,
			SUM(data_survei_pm_detail_list.jumlah_total)  as total_semua
		', FALSE);
		
		$this->db->from('data_survei_pm');
		$this->db->join('data_survei_pm_detail', 'data_survei_pm_detail.survei_pm_pm_id = data_survei_pm.id', 'left');
		$this->db->join('data_survei_pm_detail_list', 'data_survei_pm_detail_list.survei_pm_detail_id = data_survei_pm_detail.id', 'left');
		$this->db->join('m_set_wil_administratif', 'm_set_wil_administratif.wil_id = data_survei_pm_detail_list.wil_id', 'left');
		$this->db->join('master_kelompok', 'master_kelompok.id = data_survei_pm_detail_list.master_kelompok_id', 'left');
		
		$this->db->where('data_survei_pm.active', 1);
		
		// Filter berdasarkan tipe drill down - DINAMIS
		if($provinsi_code && $provinsi_code != '' && $provinsi_code != 'ALL'){
			$this->db->where("CAST(SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2) AS INTEGER) =", (int)$provinsi_code, FALSE);

		}else{
			 $this->db->where('m_set_wil_administratif.wil_prov_nama IS NOT NULL', null, FALSE);
		}
		
		if($kabkota_code && $kabkota_code != '' && $kabkota_code != 'ALL'){
			$this->db->where('m_set_wil_administratif.wil_kab_kode', $kabkota_code);
		}
		
		if($kelompok_id && $kelompok_id != '' && $kelompok_id != 'ALL'){
			$this->db->where('data_survei_pm_detail_list.master_kelompok_id', $kelompok_id);
		}
		
		if($tahun && $tahun != 'ALL'){
			$this->db->where("EXTRACT(YEAR FROM data_survei_pm.created) = ", $tahun, FALSE);
		}
		
		$this->db->group_by('
			data_survei_pm.id,
			data_survei_pm.survei_pm_nama,
			data_survei_pm.survei_pm_email,
			data_survei_pm.survei_pm_tlp,
			m_set_wil_administratif.wil_prov_nama,
			m_set_wil_administratif.wil_kab_nama,
			m_set_wil_administratif.wil_kec_nama,
			master_kelompok.nama_kelompok,
			data_survei_pm_detail_list.nama_unit,
			data_survei_pm.created,
			data_survei_pm.status_id
		');
		
		// Search
		if($searchValue){
			$this->db->group_start();
			$this->db->or_like('data_survei_pm.survei_pm_nama', $searchValue);
			$this->db->or_like('data_survei_pm.survei_pm_email', $searchValue);
			$this->db->or_like('data_survei_pm.survei_pm_tlp', $searchValue);
			$this->db->or_like('m_set_wil_administratif.wil_prov_nama', $searchValue);
			$this->db->or_like('m_set_wil_administratif.wil_kab_nama', $searchValue);
			$this->db->or_like('m_set_wil_administratif.wil_kec_nama', $searchValue);
			$this->db->or_like('master_kelompok.nama_kelompok', $searchValue);
			$this->db->or_like('data_survei_pm_detail_list.nama_unit', $searchValue);
			$this->db->group_end();
		}
		
		// Total records (filtered)
		$totalFiltered = $this->db->count_all_results('', FALSE);
		
		// Order
		if(isset($order[0]['column'])){
			$columnIndex = $order[0]['column'];
			$columnDir = $order[0]['dir'];
			
			$columns = array('id', 'survei_pm_nama', 'survei_pm_email', 'survei_pm_tlp', 'wilayah', 'kelompok', 'nama_unit','tanggal','total_pria','total_wanita','total_semua', 'status');
			
			if(isset($columns[$columnIndex])){
				if($columns[$columnIndex] == 'id'){
					$this->db->order_by('data_survei_pm.id', $columnDir);
				} else {
					$this->db->order_by($columns[$columnIndex], $columnDir);
				}
			}
		} else {
			$this->db->order_by('data_survei_pm.created', 'DESC');
		}
		
		// Limit
		if($length != -1){
			$this->db->limit($length, $start);
		}
		
		$query = $this->db->get();
		$result = $query->result_array();
		
		// Total records (unfiltered)
		$this->db->select('COUNT(DISTINCT data_survei_pm.id) as total', FALSE);
		$this->db->from('data_survei_pm');
		$this->db->join('data_survei_pm_detail', 'data_survei_pm_detail.survei_pm_pm_id = data_survei_pm.id', 'left');
		$this->db->join('data_survei_pm_detail_list', 'data_survei_pm_detail_list.survei_pm_detail_id = data_survei_pm_detail.id', 'left');
		$this->db->join('m_set_wil_administratif', 'm_set_wil_administratif.wil_id = data_survei_pm_detail_list.wil_id', 'left');
		$this->db->where('data_survei_pm.active', 1);
		
		// Apply same filters for total count
		if($provinsi_code && $provinsi_code != '' && $provinsi_code != 'ALL'){
			$this->db->where("CAST(SUBSTRING(m_set_wil_administratif.wil_prov_kode, 1, 2) AS INTEGER) =", (int)$provinsi_code, FALSE);

		}
		
		if($kabkota_code && $kabkota_code != '' && $kabkota_code != 'ALL'){
			$this->db->where('m_set_wil_administratif.wil_kab_kode', $kabkota_code);
		}
		
		if($kelompok_id && $kelompok_id != '' && $kelompok_id != 'ALL'){
			$this->db->where('data_survei_pm_detail_list.master_kelompok_id', $kelompok_id);
		}
		
		if($tahun && $tahun != 'ALL'){
			$this->db->where("EXTRACT(YEAR FROM data_survei_pm.created) = ", $tahun, FALSE);
		}
		
		$totalQuery = $this->db->get();
		$totalData = $totalQuery->row()->total ?? 0;
		
		// Format data untuk DataTables
		$data = array();
		$no = $start + 1;
		
		foreach($result as $row){
			$nestedData = array();
			$nestedData['no'] = $no;
			$nestedData['survei_pm_nama'] = $row['survei_pm_nama'];
			$nestedData['survei_pm_email'] = $row['survei_pm_email'];
			$nestedData['survei_pm_tlp'] = $row['survei_pm_tlp'];
			$nestedData['wilayah'] = $row['wilayah'];
			$nestedData['kelompok'] = $row['kelompok'];
			$nestedData['nama_unit'] = $row['nama_unit'];
			$nestedData['tanggal'] = $row['tanggal'];
			$nestedData['total_pria'] = $row['total_pria'];
			$nestedData['total_wanita'] = $row['total_wanita'];
			$nestedData['total_semua'] = $row['total_semua'];
			$nestedData['status'] = '<span class="badge badge-'.($row['status'] == 'Aktif' ? 'success' : 'secondary').'">'.$row['status'].'</span>';
			
			$data[] = $nestedData;
			$no++;
		}
		
		$json_data = array(
			"draw" => intval($draw),
			"recordsTotal" => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data" => $data,
			"csrf_hash" => $this->security->get_csrf_hash()
		);
		
		return json_encode($json_data);
	}


}