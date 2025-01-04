<?xml version="1.0" encoding="UTF-8"?>
<project>
    <name>Shop Bán Đồ Chơi Trẻ Em</name>
    <description>Dự án PHP thuần xây dựng một website bán đồ chơi trẻ em với các tính năng như quản lý tài khoản, giỏ hàng, sản phẩm, và hệ thống quản trị.</description>
    
    <technologies>
        <language>PHP</language>
        <database>MySQL</database>
        <frontend>
            <technology>HTML</technology>
            <technology>CSS</technology>
            <technology>JavaScript</technology>
            <framework>Bootstrap</framework>
        </frontend>
        <backend>
            <libraries>
                <library>firebase/php-jwt</library>
                <library>vlucas/phpdotenv</library>
            </libraries>
        </backend>
    </technologies>
    
    <features>
        <user>
            <feature>
                <name>Đăng ký</name>
                <description>Tạo tài khoản mới với thông tin cá nhân.</description>
            </feature>
            <feature>
                <name>Đăng nhập</name>
                <description>Xác thực người dùng bằng JWT và mật khẩu mã hóa.</description>
            </feature>
            <feature>
                <name>Quản lý thông tin cá nhân</name>
                <description>Xem và chỉnh sửa thông tin cá nhân.</description>
            </feature>
            <feature>
                <name>Quản lý giỏ hàng</name>
                <description>Thêm, sửa, xóa sản phẩm trong giỏ hàng.</description>
            </feature>
            <feature>
                <name>Đặt hàng</name>
                <description>Đặt hàng từ giỏ hàng, quản lý đơn hàng và xem chi tiết đơn hàng.</description>
            </feature>
            <feature>
                <name>Đánh giá sản phẩm</name>
                <description>Thêm, xem, và xóa đánh giá sản phẩm.</description>
            </feature>
        </user>
        <admin>
            <feature>
                <name>Quản lý sản phẩm</name>
                <description>Thêm, sửa, và xóa sản phẩm, bao gồm hình ảnh.</description>
            </feature>
            <feature>
                <name>Quản lý danh mục</name>
                <description>Thêm, sửa, và xóa danh mục sản phẩm.</description>
            </feature>
            <feature>
                <name>Quản lý đơn hàng</name>
                <description>Xem danh sách đơn hàng, cập nhật trạng thái, và xem chi tiết đơn hàng.</description>
            </feature>
            <feature>
                <name>Quản lý người dùng</name>
                <description>Xem danh sách người dùng và xóa người dùng.</description>
            </feature>
        </admin>
    </features>
    
    <setup>
        <step>
            <name>Clone repository từ Git</name>
            <command>git clone &lt;URL của repository&gt;</command>
            <description>Clone mã nguồn dự án từ GitHub về máy cục bộ.</description>
        </step>
        <step>
            <name>Cài đặt thư viện Composer</name>
            <command>composer install</command>
            <description>Cài đặt các thư viện PHP cần thiết bằng Composer.</description>
        </step>
        <step>
            <name>Cấu hình file .env</name>
            <description>Tạo file .env để lưu thông tin cấu hình cơ sở dữ liệu, JWT, và mã hóa AES.</description>
            <example>
                DB_HOST=127.0.0.1
                DB_NAME=toy_shop
                DB_USER=root
                DB_PASSWORD=
                JWT_SECRET=your_secret_key
                ENCRYPTION_KEY=32_character_secret_key
                ENCRYPTION_IV=16_character_iv
            </example>
        </step>
        <step>
            <name>Tạo cơ sở dữ liệu</name>
            <command>mysql -u root -p toy_shop &lt; database.sql</command>
            <description>Nhập file SQL để tạo bảng trong cơ sở dữ liệu MySQL.</description>
        </step>
        <step>
            <name>Khởi động server PHP</name>
            <command>php -S localhost:8000 -t public</command>
            <description>Khởi chạy server PHP để truy cập ứng dụng qua trình duyệt.</description>
        </step>
    </setup>
    
    <routes>
        <frontend>
            <route>
                <url>/index.php?page=home</url>
                <description>Trang chủ hiển thị danh sách sản phẩm.</description>
            </route>
            <route>
                <url>/index.php?page=product&id={product_id}</url>
                <description>Trang chi tiết sản phẩm.</description>
            </route>
            <route>
                <url>/index.php?page=cart</url>
                <description>Trang giỏ hàng của người dùng.</description>
            </route>
            <route>
                <url>/index.php?page=checkout</url>
                <description>Trang thanh toán đơn hàng.</description>
            </route>
            <route>
                <url>/index.php?page=profile</url>
                <description>Trang thông tin cá nhân người dùng.</description>
            </route>
            <route>
                <url>/index.php?page=orders</url>
                <description>Trang danh sách đơn hàng của người dùng.</description>
            </route>
        </frontend>
        <api>
            <route>
                <url>/api/users</url>
                <method>GET</method>
                <description>Lấy danh sách người dùng.</description>
            </route>
            <route>
                <url>/api/products</url>
                <method>GET</method>
                <description>Lấy danh sách sản phẩm.</description>
            </route>
            <route>
                <url>/api/login</url>
                <method>POST</method>
                <description>API đăng nhập người dùng, trả về token JWT.</description>
            </route>
            <route>
                <url>/api/register</url>
                <method>POST</method>
                <description>API đăng ký tài khoản người dùng mới.</description>
            </route>
            <route>
                <url>/api/orders</url>
                <method>POST</method>
                <description>API đặt hàng.</description>
            </route>
        </api>
    </routes>
    
    <commands>
        <command>
            <name>Khởi động server PHP</name>
            <value>php -S localhost:8000 -t public</value>
        </command>
        <command>
            <name>Cài đặt thư viện</name>
            <value>composer install</value>
        </command>
        <command>
            <name>Import cơ sở dữ liệu</name>
            <value>mysql -u root -p toy_shop &lt; database.sql</value>
        </command>
    </commands>
</project>
