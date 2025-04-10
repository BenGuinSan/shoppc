<?php
// Đường dẫn tương đối đến file authentication.php
require_once './api/taikhoan/authentication.php';
require_once './api/NguoiDungController.php';
require_once './api/NhomQuyenController.php';
require_once './api/ChucNangController.php';
require_once './api/NhaCungCapController.php';
require_once './api/LoaiSanPhamController.php';
require_once './api/taikhoan/TaiKhoanController.php';
// Thiết lập header JSON
header("Content-Type: application/json");

// Xử lý CORS (cho phép truy cập từ các domain khác)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


// Khởi tạo controller
$authController = new AuthController();
$nguoiDungController = new NguoiDungController();
$nhomQuyenController = new NhomQuyenController();
$chucNangController = new ChucNangController();
$nhaCungCapController = new NhaCungCapController();
$loaiSanPhamController = new LoaiSanPhamController();
$taiKhoanController = new TaiKhoanController();


error_log($_SERVER['REQUEST_URI']);

// Lấy URI và phương thức request
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$requestMethod = $_SERVER['REQUEST_METHOD'];

error_log($requestUri);

// Xác định base path của API
$basePath = '/shop_pc/api'; // Sửa lại base path cho đúng
$apiPath = str_replace($basePath, '', $requestUri);

error_log("API Path: " . $apiPath);


switch ($apiPath) {
    case '/auth/register':
        if ($requestMethod === 'POST') {
            $authController->register();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/auth/login':
        if ($requestMethod === 'POST') {
            $authController->login();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/user/profile':
        if ($requestMethod === 'GET') {
            $nguoiDungController->getCurrentUser();
        } else if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $nguoiDungController->updateCurrentUser();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // nguoi dung tu doi mat khau
    case '/user/password':
        if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $taiKhoanController->updatePassword();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // nguoi dung tu vo hieu/kich hoat tai khoan co the khong can thiet
    case '/user/deactivate':
        if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $taiKhoanController->deactivateOwnAccount();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/accounts':
        if ($requestMethod === 'GET') {
            $taiKhoanController->getAllAccounts();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('#^/accounts/([^/]+)$#', $apiPath, $matches) ? true : false):
        $maTaiKhoan = $matches[1];

        if ($requestMethod === 'GET') {
            $taiKhoanController->getAccountById($maTaiKhoan);
        } else if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $taiKhoanController->updateAccount($maTaiKhoan);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('#^/accounts/([^/]+)/role$#', $apiPath, $matches) ? true : false):
        $maTaiKhoan = $matches[1];

        if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $taiKhoanController->updateAccountRole($maTaiKhoan);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/users':
        if ($requestMethod === 'GET') {
            $nguoiDungController->getAllUsers();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // Url path động thì sử dụng biểu thử preg_match
    case (preg_match('#^/users/([^/]+)$#', $apiPath, $matches) ? $apiPath : ''):
        $maNguoiDung = $matches[1];

        if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $nguoiDungController->updateUser($maNguoiDung);
        } else if ($requestMethod === 'DELETE') {
            $nguoiDungController->deleteUser($maNguoiDung);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // Nhom quyen route
    case '/nhomquyen':
        if ($requestMethod === 'GET') {
            $nhomQuyenController->getAll();
        } else if ($requestMethod === 'POST') {
            $nhomQuyenController->create();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('#^/nhomquyen/([^/]+)$#', $apiPath, $matches) ? true : false):
        $maNhomQuyen = $matches[1];

        if ($requestMethod === 'GET') {
            $nhomQuyenController->getOne($maNhomQuyen);
        } else if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $nhomQuyenController->update($maNhomQuyen);
        } else if ($requestMethod === 'DELETE') {
            $nhomQuyenController->delete($maNhomQuyen);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('#^/nhomquyen/([^/]+)/chucnang$#', $apiPath, $matches) ? true : false):
        $maNhomQuyen = $matches[1];

        if ($requestMethod === 'GET') {
            $nhomQuyenController->getFunctions($maNhomQuyen);
        } else if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $nhomQuyenController->updateFunctions($maNhomQuyen);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // chuc nang route
    case '/chucnang':
        if ($requestMethod === 'GET') {
            $chucNangController->getAll();
        } else if ($requestMethod === 'POST') {
            $chucNangController->create();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('#^/chucnang/([^/]+)$#', $apiPath, $matches) ? true : false):
        $maChucNang = $matches[1];

        if ($requestMethod === 'GET') {
            $chucNangController->getOne($maChucNang);
        } else if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $chucNangController->update($maChucNang);
        } else if ($requestMethod === 'DELETE') {
            $chucNangController->delete($maChucNang);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // nha cung cap route
    case '/nhacungcap':
        if ($requestMethod === 'GET') {
            $nhaCungCapController->getAll();
        } else if ($requestMethod === 'POST') {
            $nhaCungCapController->create();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('#^/nhacungcap/([^/]+)$#', $apiPath, $matches) ? true : false):
        $maNhaCungCap = $matches[1];

        if ($requestMethod === 'GET') {
            $nhaCungCapController->getOne($maNhaCungCap);
        } else if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $nhaCungCapController->update($maNhaCungCap);
        } else if ($requestMethod === 'DELETE') {
            $nhaCungCapController->delete($maNhaCungCap);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // loai san pham route
    case '/loaisanpham':
        if ($requestMethod === 'GET') {
            $loaiSanPhamController->getAll();
        } else if ($requestMethod === 'POST') {
            $loaiSanPhamController->create();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case (preg_match('#^/loaisanpham/([^/]+)$#', $apiPath, $matches) ? true : false):
        $maLoaiSanPham = $matches[1];

        if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $loaiSanPhamController->update($maLoaiSanPham);
        } else if ($requestMethod === 'DELETE') {
            $loaiSanPhamController->delete($maLoaiSanPham);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    




    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found', 'path' => $apiPath]);
        break;
}
