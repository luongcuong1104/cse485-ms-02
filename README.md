# MiniShop — Catalog (Buổi 1)

Dự án nhỏ hiển thị danh mục sản phẩm (Catalog) viết bằng PHP cơ bản, phục vụ cho Buổi 1 (Phiếu 01).

## Thành phần Project

- **`data.php`**: Khai báo hằng số `STORE_NAME` và các mảng dữ liệu nhiều chiều đại diện cho danh mục (`$categories`) và danh sách 8 sản phẩm (`$products`).
- **`index.php`**: Yêu cầu (require) dữ liệu từ `data.php`, thực hiện tính toán giá trị tồn kho, ánh xạ tên danh mục và render cấu trúc HTML bảng sản phẩm.
- **`style.css`**: Chứa toàn bộ các định dạng CSS cho giao diện, sử dụng font chữ Inter, màu sắc hiện đại, hiệu ứng hover dòng bảng màu xanh nhạt và căn lề thân thiện.

## Cách chạy dự án dưới Local

1. Khởi động module Apache trong **XAMPP Control Panel**.
2. Di chuyển/sao chép thư mục `minishop-01` vào thư mục gốc `htdocs` của XAMPP (mặc định là `C:\xampp\htdocs\minishop-01`).
3. Truy cập trình duyệt theo địa chỉ: `http://localhost/minishop-01/index.php`.
