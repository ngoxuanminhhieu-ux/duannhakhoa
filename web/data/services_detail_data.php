<?php
/**
 * Chi tiết toàn diện 10 dịch vụ Nha khoa:
 * - Gồm các bước gì (Quy trình y khoa từng bước)
 * - Làm như thế nào (Công nghệ kỹ thuật, máy móc, phương pháp thực hiện)
 * - Tác dụng ra sao (Lợi ích, tác dụng lâm sàng và thẩm mỹ)
 */

function get_all_services_details() {
    return [
        1 => [
            'id' => 1,
            'name' => 'Khám & Tư vấn Răng tổng quát',
            'category' => 'Dịch Vụ Tổng Quát & Phòng Ngừa',
            'tagline' => 'Chẩn đoán hình ảnh kỹ thuật số 3D & Lập kế hoạch điều trị cá nhân hóa',
            'price_from' => 100000,
            'price_formatted' => '100.000đ',
            'duration' => '30 - 45 phút',
            'pain_level' => 'Hoàn toàn không đau, nhẹ nhàng, thoải mái',
            'warranty' => 'Tư vấn & lưu trữ hồ sơ bệnh án số trọn đời',
            'image' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=800&auto=format&fit=crop&q=80',
            'technology' => 'Máy chụp phim CT Cone Beam 3D thế hệ mới, Máy scan trong miệng iTero Element 5D, Camera nội soi nha khoa HD',
            'how_it_works' => 'Bác sĩ sử dụng hệ thống chụp phim 3D kỹ thuật số đa chiều và máy scan khoang miệng để ghi lại cấu trúc xương hàm, hệ thống chân răng, phát hiện các ổ sâu răng kẽ hay tổn thương tiềm ẩn mắt thường không thấy được. Toàn bộ dữ liệu được số hóa trên màn hình y khoa lớn để bác sĩ cùng bệnh nhân trực tiếp phân tích.',
            'effects' => [
                'Tác dụng phát hiện sớm 100% các bệnh lý răng miệng nguy hiểm: sâu răng sớm, viêm tủy kín, nang xương hàm, tiêu xương quanh răng.',
                'Đánh giá chính xác khớp cắn, tương quan hai hàm và vị trí mọc của răng khôn để có hướng xử lý kịp thời trước khi biến chứng.',
                'Được bác sĩ chuyên khoa Răng Hàm Mặt lập phác đồ điều trị chi tiết, rõ ràng chi phí từ đầu, không phát sinh chi phí phụ.',
                'Tiết kiệm tối đa chi phí điều trị trong tương lai nhờ can thiệp từ giai đoạn sớm.'
            ],
            'target_audience' => 'Mọi lứa tuổi từ trẻ em đến người lớn tuổi. Khuyến nghị thăm khám định kỳ 6 tháng/lần hoặc khi cảm thấy ê buốt, khó chịu nhẹ khi ăn nhai.',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Tiếp đón & Thăm khám tổng quan lâm sàng',
                    'desc' => 'Lễ tân tiếp đón chu đáo, hướng dẫn đo huyết áp và lập hồ sơ bệnh án điện tử. Bác sĩ trực tiếp kiểm tra tổng quát mô nướu, niêm mạc, răng và khớp thái dương hàm.'
                ],
                [
                    'step' => 2,
                    'title' => 'Chụp phim X-quang kỹ thuật số Cone Beam 3D',
                    'desc' => 'Bệnh nhân được chụp phim X-quang toàn cảnh (Panorama) hoặc CT 3D với liều tia cực thấp an toàn tuyệt đối, thu hình ảnh sắc nét trong vòng 10 giây.'
                ],
                [
                    'step' => 3,
                    'title' => 'Phân tích dữ liệu & Hội chẩn cùng Bác sĩ',
                    'desc' => 'Bác sĩ hiển thị hình ảnh 3D trên màn hình, giải thích chi tiết hiện trạng từng chiếc răng, mức độ mảng bám, nguy cơ sâu răng và các vị trí cần lưu ý.'
                ],
                [
                    'step' => 4,
                    'title' => 'Lập phác đồ điều trị & Báo giá minh bạch',
                    'desc' => 'Đưa ra các phương án điều trị phù hợp nhất (kèm ưu/nhược điểm từng giải pháp), lập kế hoạch thời gian và bảng chi phí niêm yết rõ ràng.'
                ],
                [
                    'step' => 5,
                    'title' => 'Hướng dẫn vệ sinh chăm sóc & Cung cấp hồ sơ số',
                    'desc' => 'Bác sĩ hướng dẫn phương pháp chải răng đúng cách, chỉ nha khoa và gửi hồ sơ bệnh án kỹ thuật số về tài khoản của bệnh nhân.'
                ]
            ],
            'post_care' => 'Duy trì đánh răng 2 lần/ngày bằng bàn chải lông mềm, sử dụng chỉ nha khoa hoặc máy tăm nước, tái khám định kỳ mỗi 6 tháng.',
            'doctor_specialist' => 'ThS.BS Nguyễn Văn An (Trưởng khoa Răng Hàm Mặt, 12 năm kinh nghiệm)',
            'faqs' => [
                ['q' => 'Chụp X-quang răng có ảnh hưởng đến sức khỏe không?', 'a' => 'Hoàn toàn an toàn! Máy chụp CT Cone Beam tại Premier Dental sử dụng cảm biến kỹ thuật số hiện đại, liều lượng bức xạ thấp hơn 80% so với máy truyền thống, đạt tiêu chuẩn an toàn y tế quốc tế.'],
                ['q' => 'Thời gian khám tổng quát mất bao lâu?', 'a' => 'Toàn bộ quy trình khám, chụp phim và tư vấn diễn ra trong khoảng 30 - 45 phút, bệnh nhân không phải chờ đợi.'],
                ['q' => 'Khám xong tôi có bắt buộc phải làm dịch vụ ngay không?', 'a' => 'Hoàn toàn không. Premier Dental tôn trọng quyền quyết định của khách hàng. Bạn nhận hồ sơ tư vấn chi tiết để tham khảo và chỉ thực hiện khi cảm thấy sẵn sàng.']
            ]
        ],

        2 => [
            'id' => 2,
            'name' => 'Cạo vôi răng & Đánh bóng',
            'category' => 'Dịch Vụ Tổng Quát & Phòng Ngừa',
            'tagline' => 'Công nghệ sóng siêu âm Piezotome êm ái, sạch mảng bám dưới nướu',
            'price_from' => 250000,
            'price_formatted' => '250.000đ',
            'duration' => '30 - 40 phút',
            'pain_level' => 'Hoàn toàn êm dịu, không rách nướu, hạn chế tối đa ê buốt',
            'warranty' => 'Bảo hành kiểm tra nướu định kỳ miễn phí',
            'image' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=800&auto=format&fit=crop&q=80',
            'technology' => 'Đầu rung sóng siêu âm thông minh tần số tự điều chỉnh Piezotome kết hợp tia phun sương vô trùng áp lực nhẹ',
            'how_it_works' => 'Thay vì dùng lực cơ học cạo nạo truyền thống dễ làm tổn thương men răng, máy Piezotome sử dụng các bước sóng siêu âm dao động nhẹ nhàng làm gãy vỡ liên kết của các mảng vôi răng cứng đầu bám sâu dưới nướu. Tia nước vô trùng đồng thời rửa trôi và làm dịu vùng nướu tức thì.',
            'effects' => [
                'Tác dụng làm sạch 100% vôi răng và vi khuẩn kỵ khí bám quanh chân răng và rãnh lợi.',
                'Ngăn ngừa dứt điểm nguy cơ viêm nướu, viêm nha chu, tụt lợi làm lộ chân răng dẫn đến lung lay răng.',
                'Đánh bay mùi hôi miệng do vi khuẩn tích tụ lâu ngày, mang lại hơi thở thơm mát, tự tin.',
                'Bề mặt men răng sau khi đánh bóng trở nên trơn láng, hạn chế bám dính mảng bám cà phê, trà và thực phẩm mới.'
            ],
            'target_audience' => 'Tất cả mọi người từ 12 tuổi trở lên. Người có nhiều cao răng, hút thuốc, uống trà cà phê thường xuyên hoặc có triệu chứng chảy máu chân răng khi đánh răng.',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Thăm khám tình trạng nướu và mức độ vôi răng',
                    'desc' => 'Bác sĩ kiểm tra mức độ tích tụ của vôi răng (độ 1, độ 2 hoặc vôi dưới nướu sâu), đánh giá sức khỏe của các túi nha chu.'
                ],
                [
                    'step' => 2,
                    'title' => 'Sát khuẩn khoang miệng bằng dung dịch y khoa',
                    'desc' => 'Cho bệnh nhân súc miệng bằng dung dịch sát khuẩn chuyên dụng để vô trùng bề mặt khoang miệng trước khi thao tác.'
                ],
                [
                    'step' => 3,
                    'title' => 'Cạo vôi răng bằng sóng siêu âm Piezotome',
                    'desc' => 'Di chuyển đầu sóng siêu âm êm ái qua từng kẽ răng, thân răng và dưới viền nướu. Mảng bám tự động rã ra nhẹ nhàng mà không tổn hại men răng.'
                ],
                [
                    'step' => 4,
                    'title' => 'Đánh bóng men răng bằng chổi chuyên dụng',
                    'desc' => 'Dùng gel đánh bóng chứa flour sinh học cùng chổi lông siêu mềm làm nhẵn bề mặt răng, giúp răng sáng bóng tự nhiên và chống tái bám vôi.'
                ],
                [
                    'step' => 5,
                    'title' => 'Kiểm tra khớp cắn và hướng dẫn chăm sóc nướu',
                    'desc' => 'Súc miệng sạch hoàn toàn, bác sĩ kiểm tra lại độ săn chắc của viền nướu và hướng dẫn chế độ ăn uống 24h sau lấy vôi.'
                ]
            ],
            'post_care' => 'Hạn chế dùng thực phẩm quá nóng, quá lạnh hoặc sẫm màu trong 2-3 giờ đầu. Đánh răng nhẹ nhàng với bàn chải lông mềm.',
            'doctor_specialist' => 'BS. CKI Đỗ Minh Đức (10 năm kinh nghiệm)',
            'faqs' => [
                ['q' => 'Cạo vôi răng có làm mòn men răng hoặc thưa răng không?', 'a' => 'Tuyệt đối không! Sóng siêu âm Piezotome chỉ rung rã các mảng bám vôi răng bên ngoài, hoàn toàn không chạm mòn men răng. Răng có cảm giác "thoáng hơn" là do khối vôi răng trước đó chiếm chỗ đã được làm sạch.'],
                ['q' => 'Bao lâu nên đi lấy cao răng một lần?', 'a' => 'Thời gian lý tưởng là 6 tháng/lần đối với người bình thường, và 3-4 tháng/lần đối với người hút thuốc hoặc có cơ địa vôi răng nhanh hình thành.']
            ]
        ],

        3 => [
            'id' => 3,
            'name' => 'Trám răng thẩm mỹ Composite',
            'category' => 'Nha Khoa Điều Trị & Tiểu Phẫu',
            'tagline' => 'Phục hồi răng sâu & sứt mẻ với vật liệu Composite hạt Nano đồng màu răng thật',
            'price_from' => 350000,
            'price_formatted' => '350.000đ / răng',
            'duration' => '20 - 30 phút / răng',
            'pain_level' => 'Không đau, làm sạch xoang sâu nhẹ nhàng êm ái',
            'warranty' => 'Bảo hành chính hãng 2 năm (trám lại miễn phí nếu bong tróc)',
            'image' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=800&auto=format&fit=crop&q=80',
            'technology' => 'Vật liệu hạt độn Nano Composite 3M Filtek (USA), Đèn quang trùng hợp LED quang phổ rộng đông cứng siêu tốc trong 20s',
            'how_it_works' => 'Bác sĩ làm sạch hoàn toàn mô răng bị sâu mục, tạo hình khoang trám vi phẫu bảo tồn tối đa cấu trúc răng lành. Sau đó, bôi keo dán sinh học Bonding thế hệ 7 và đắp từng lớp Composite có màu sắc đối chiếu bảng so màu răng thật Vita. Chiếu đèn quang trùng hợp làm chất liệu đông cứng tức thì thành một khối vững chắc.',
            'effects' => [
                'Tác dụng loại bỏ triệt để vi khuẩn sâu răng, ngăn chặn sâu răng ăn sâu vào buồng tủy gây viêm tủy đau đớn.',
                'Phục hồi nguyên vẹn hình dáng giải phẫu, các gờ rãnh cắn và màu sắc tự nhiên của răng thật đến 99%.',
                'Khôi phục chức năng ăn nhai chắc chắn ngay sau khi hoàn tất, không phải kiêng nhai lâu ngày.',
                'Tái tạo thẩm mỹ hoàn hảo cho các răng cửa bị thưa kẽ nhẹ, mẻ góc hoặc mòn men răng.'
            ],
            'target_audience' => 'Người bị sâu răng mặt nhai hoặc kẽ răng, răng bị mòn cổ chân răng gây ê buốt khi uống nước lạnh, răng cửa bị sứt mẻ nhỏ do chấn thương.',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Thăm khám & Xác định xoang sâu',
                    'desc' => 'Bác sĩ kiểm tra kích thước lỗ sâu và kiểm tra độ sâu có chạm tủy không thông qua mắt soi và phim X-quang.'
                ],
                [
                    'step' => 2,
                    'title' => 'Làm sạch mô sâu & Sát khuẩn xoang trám',
                    'desc' => 'Dùng mũi khoan vi phẫu nạo sạch hoàn toàn vụn thức ăn và ngà sâu hoại tử, khử trùng khoang trám bằng dung dịch y khoa.'
                ],
                [
                    'step' => 3,
                    'title' => 'So màu & Xử lý bề mặt men răng (Etching & Bonding)',
                    'desc' => 'Chọn tông màu Composite trùng khớp 100% với răng kế cận. Xử lý axit nhẹ tạo độ bám vi thể và quét keo dán sinh học liên kết vĩnh viễn.'
                ],
                [
                    'step' => 4,
                    'title' => 'Đắp chất liệu Composite & Chiếu đèn quang trùng hợp',
                    'desc' => 'Đắp chất liệu theo từng lớp mỏng, khéo léo tạo hình các múi răng tự nhiên và chiếu đèn LED 20 giây để đông cứng vật liệu.'
                ],
                [
                    'step' => 5,
                    'title' => 'Chỉnh khớp cắn & Đánh bóng bề mặt miếng trám',
                    'desc' => 'Dùng giấy cắn kiểm tra không bị cộm vướng, mài mịn và đánh bóng để miếng trám sáng bóng tự nhiên, không dính thức ăn.'
                ]
            ],
            'post_care' => 'Có thể ăn nhai nhẹ nhàng sau 30 phút. Tránh cắn đồ ăn quá cứng (đá lạnh, nắp chai, xương cứng) bằng răng vừa trám.',
            'doctor_specialist' => 'ThS.BS Nguyễn Văn An (12 năm kinh nghiệm)',
            'faqs' => [
                ['q' => 'Miếng trám Composite có bền không và dùng được bao lâu?', 'a' => 'Vật liệu Composite Nano 3M tại Premier Dental có độ bền trung bình từ 5 - 7 năm hoặc lâu hơn nếu chăm sóc tốt, có chế độ bảo hành 2 năm an tâm tuyệt đối.'],
                ['q' => 'Trám răng có bị đau nhức không?', 'a' => 'Các ca sâu ngà nông hoàn toàn không đau. Với xoang sâu sâu gần tủy, bác sĩ sẽ thoa gel tê cục bộ nhẹ nhàng trước khi làm sạch nên bạn sẽ không cảm thấy đau buốt.']
            ]
        ],

        4 => [
            'id' => 4,
            'name' => 'Nhổ răng thường / Răng khôn',
            'category' => 'Nha Khoa Điều Trị & Tiểu Phẫu',
            'tagline' => 'Tiểu phẫu sóng siêu âm Piezotome không đau, không rách mô mềm, lành thương nhanh',
            'price_from' => 500000,
            'price_formatted' => 'Từ 500.000đ (Răng thường) - 1.500.000đ (Răng khôn)',
            'duration' => '15 - 30 phút / răng',
            'pain_level' => 'Gây tê đa tầng không đau, sau nhổ êm dịu hạn chế tối đa sưng má',
            'warranty' => 'Theo dõi hậu phẫu & cắt chỉ, tái khám miễn phí 100%',
            'image' => 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=800&auto=format&fit=crop&q=80',
            'technology' => 'Máy phẫu thuật siêu âm xương Piezotome, Màng huyết tương giàu tiểu cầu PRF kích thích lành thương nhanh gấp 3 lần',
            'how_it_works' => 'Bác sĩ ứng dụng tần số sóng siêu âm tinh vi để làm đứt nhẹ các dây chằng nha chu neo giữ quanh chân răng. Răng lung lay tự nhiên và được nhấc ra nhẹ nhàng mà không cần dùng búa, kìm gõ đập thô bạo. Hốc nhổ răng được đặt màng PRF tự thân để cầm máu tức thì và làm đầy xương ổ răng.',
            'effects' => [
                'Tác dụng giải phóng tức thì cơn đau nhức dữ dội, đau nửa đầu do răng khôn mọc lệch, mọc ngầm, đâm ngang gây ra.',
                'Ngăn chặn nguy cơ răng khôn đẩy xô lệch toàn bộ cung hàm hoặc đâm thủng làm hỏng răng hàm số 7 kế cận.',
                'Triệt tiêu các ổ viêm lợi trùm, viêm mô tế bào và áp xe chân răng nguy hiểm.',
                'Công nghệ Piezotome bảo tồn 100% mô nướu và xương hàm xung quanh, giúp vết thương lành cực nhanh sau 24 - 48h.'
            ],
            'target_audience' => 'Người mọc răng số 8 (răng khôn) đau nhức, răng khôn mọc kẹt dưới nướu, răng hàm bị sâu vỡ mục lớn không thể bảo tồn, hoặc nhổ răng theo chỉ định chỉnh nha.',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Chụp phim CT 3D chẩn đoán vị trí chân răng & dây thần kinh',
                    'desc' => 'Khảo sát 3 chiều cấu trúc chân răng khôn, hướng mọc và khoảng cách an toàn với ống dây thần kinh hàm dưới.'
                ],
                [
                    'step' => 2,
                    'title' => 'Vệ sinh vô trùng & Gây tê đa tầng kỹ thuật số',
                    'desc' => 'Thoa gel tê bề mặt và gây tê cục bộ không đau bằng mũi kim siêu mảnh, giúp khu vực răng nhổ tê sâu hoàn toàn.'
                ],
                [
                    'step' => 3,
                    'title' => 'Bóc tách dây chằng bằng sóng siêu âm Piezotome',
                    'desc' => 'Đầu máy Piezotome rung tần số cao nhẹ nhàng lướt quanh chân răng, làm lỏng chân răng êm ái mà không gây sang chấn mô mềm.'
                ],
                [
                    'step' => 4,
                    'title' => 'Nhấc răng ra ngoài & Đặt màng lành thương PRF',
                    'desc' => 'Nhấc răng nhẹ nhàng ra khỏi huyệt ổ răng, bơm rửa sát khuẩn và ghép màng PRF hỗ trợ cầm máu tức thì và kích thích tạo tế bào mới.'
                ],
                [
                    'step' => 5,
                    'title' => 'Khâu thẩm mỹ bằng chỉ tự tiêu & Hướng dẫn hậu phẫu',
                    'desc' => 'Khâu kín vết thương bằng chỉ y tế siêu nhỏ, ngậm gạc tiệt trùng 30 phút và nhận túi chườm lạnh cùng đơn thuốc hướng dẫn tại nhà.'
                ]
            ],
            'post_care' => 'Cắn chặt gạc 30-45 phút, chườm lạnh ngoài má trong ngày đầu tiên, ăn cháo súp nguội mềm, không súc miệng nước muối mạnh hay dùng ống hút trong 24h đầu.',
            'doctor_specialist' => 'BS. CKI Đỗ Minh Đức (Chuyên gia phẫu thuật miệng, hơn 3.000 ca răng khôn an toàn)',
            'faqs' => [
                ['q' => 'Nhổ răng khôn bằng Piezotome có bị sưng má to không?', 'a' => 'Nhờ sóng siêu âm không làm dập nát mạch máu và mô nướu, kết hợp màng PRF nên tỷ lệ sưng nề giảm đến 85% so với nhổ truyền thống. Phần lớn bệnh nhân có thể đi học, đi làm bình thường vào ngày hôm sau.'],
                ['q' => 'Có nên nhổ răng khôn khi đang bị sưng viêm đau không?', 'a' => 'Khi đang có ổ sưng viêm cấp, bác sĩ sẽ tiến hành vệ sinh, kê đơn thuốc giảm đau chống viêm trước 2-3 ngày, sau đó mới tiến hành nhổ để đảm bảo hiệu quả gây tê tốt nhất.']
            ]
        ],

        5 => [
            'id' => 5,
            'name' => 'Điều trị tủy răng',
            'category' => 'Nha Khoa Điều Trị & Tiểu Phẫu',
            'tagline' => 'Triệt căn vi khuẩn ống tủy bằng máy đo vi phẫu, dứt cơn đau buốt, bảo tồn răng thật',
            'price_from' => 800000,
            'price_formatted' => '800.000đ - 1.800.000đ / răng',
            'duration' => '1 - 2 buổi hẹn (45 phút / buổi)',
            'pain_level' => 'Gây tê êm ái, chấm dứt hoàn toàn cơn đau tủy dai dẳng',
            'warranty' => 'Bảo hành chụp phim kiểm tra chóp răng định kỳ',
            'image' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=800&auto=format&fit=crop&q=80',
            'technology' => 'Máy định vị chóp điện tử đa tần số Propex, Hệ thống trâm quay Ni-Ti dẻo dai WaveOne Gold, Máy nong rửa siêu âm kích hoạt dung dịch sát khuẩn',
            'how_it_works' => 'Bác sĩ mở đường vào buồng tủy, sử dụng máy định vị chóp đo chính xác chiều dài từng ống tủy đến 0.1mm. Trâm quay dẻo cao cấp lấy sạch mô tủy viêm nhiễm hoại tử, kết hợp sóng siêu âm làm sạch sâu lòng ống tủy. Sau đó, trám bít kín 3 chiều bằng chất liệu Gutta-Percha sinh học ngăn ngừa vi khuẩn tái xâm nhập.',
            'effects' => [
                'Tác dụng dứt điểm 100% cơn đau nhức buốt răng lên tận óc, đau giật theo nhịp tim khi ăn nóng/lạnh hoặc nằm ngủ.',
                'Triệt tiêu ổ viêm nhiễm quanh chóp răng, bảo vệ xương ổ răng không bị tiêu biến hoặc hình thành u hạt, nang chân răng.',
                'Cứu sống và bảo tồn răng thật, tránh nguy cơ phải nhổ bỏ răng và tốn kém chi phí trồng răng giả sau này.',
                'Khôi phục răng có thể thực hiện chức năng ăn nhai bình thường sau khi bọc bảo vệ.'
            ],
            'target_audience' => 'Người bị sâu răng ăn sâu vào tủy, răng bị đau nhức dữ dội tự phát về đêm, răng bị chấn thương mẻ lớn lộ tủy đỏ, răng đổi màu xám sẫm do chết tủy.',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Khám lâm sàng & Đo độ nhạy tủy qua phim X-quang',
                    'desc' => 'Xác định số lượng và hình dạng các ống tủy (răng cửa 1 ống tủy, răng cối 3-4 ống tủy phức tạp).'
                ],
                [
                    'step' => 2,
                    'title' => 'Cách ly răng bằng đê cao su & Gây tê cục bộ',
                    'desc' => 'Đặt đê cao su bảo vệ chống nhiễm khuẩn nước bọt và thoa thuốc tê giúp bệnh nhân hoàn toàn thư giãn không đau.'
                ],
                [
                    'step' => 3,
                    'title' => 'Mở buồng tủy & Đo chiều dài làm việc bằng máy định vị',
                    'desc' => 'Mở lối vào buồng tủy, dùng máy định vị điện tử xác định chính xác chiều dài từng ống tủy đến tận chóp chân răng.'
                ],
                [
                    'step' => 4,
                    'title' => 'Tạo hình & Khử trùng ống tủy bằng sóng siêu âm',
                    'desc' => 'Trâm quay Ni-Ti làm sạch và mở rộng ống tủy, kết hợp bơm rửa dung dịch NaOCl kích hoạt sóng siêu âm triệt khuẩn 99.9%.'
                ],
                [
                    'step' => 5,
                    'title' => 'Trám bít ống tủy kín khít 3 chiều & Hẹn phục hình',
                    'desc' => 'Trám bít bằng vật liệu Gutta-Percha sinh học, chụp phim kiểm tra độ kín chóp và hàn kín thân răng (khuyến nghị bọc sứ bảo vệ răng tủy).'
                ]
            ],
            'post_care' => 'Tránh cắn vật cứng bên hàm vừa chữa tủy. Răng sau khi lấy tủy mất nguồn nuôi dưỡng nên giòn hơn răng sống, nên bọc răng sứ để bảo vệ thân răng không bị nứt vỡ.',
            'doctor_specialist' => 'ThS.BS Nguyễn Văn An (12 năm kinh nghiệm điều trị nội nha vi phẫu)',
            'faqs' => [
                ['q' => 'Điều trị tủy răng có đau như người ta thường đồn không?', 'a' => 'Với công nghệ gây tê hiện đại và máy đo chóp định vị tại Premier Dental, quá trình lấy tủy diễn ra hoàn toàn êm ái, không hề có cảm giác đau đớn như các phương pháp thô sơ thời xưa.'],
                ['q' => 'Răng sau khi chữa tủy xong có cần bọc sứ không?', 'a' => 'Rất nên! Răng đã lấy tủy không còn mạch máu nuôi dưỡng nên sẽ dần mất nước và giòn theo thời gian. Bọc một mão sứ bên ngoài như một lớp áo giáp giúp răng chịu lực nhai hoàn hảo suốt nhiều thập kỷ.']
            ]
        ],

        6 => [
            'id' => 6,
            'name' => 'Tẩy trắng răng (Plasma / Laser)',
            'category' => 'Nha Khoa Thẩm Mỹ & Phục Hình',
            'tagline' => 'Bật sáng 3 - 5 tông nụ cười rạng rỡ với ánh sáng lạnh Laser Whitening không ê buốt',
            'price_from' => 1500000,
            'price_formatted' => '1.500.000đ - 2.500.000đ',
            'duration' => '45 - 60 phút',
            'pain_level' => 'Có thoa gel chống ê buốt chuyên dụng, êm ái dịu nhẹ',
            'warranty' => 'Cam kết bật tông màu chuẩn thước đo Vita Master',
            'image' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=800&auto=format&fit=crop&q=80',
            'technology' => 'Hệ thống đèn Laser Whitening ánh sáng xanh đơn sắc mát lạnh, Gel tẩy trắng y khoa Opalescence / Pola Office nhập khẩu chính hãng (USA/Úc)',
            'how_it_works' => 'Thuốc tẩy trắng chứa các hạt Hydrogen Peroxide thẩm thấu sâu vào cấu trúc men răng. Khi chiếu ánh sáng Laser bước sóng chọn lọc, các phản ứng oxy hóa bẻ gãy hoàn toàn các chuỗi protein mang màu ố vàng tích tụ lâu năm mà không làm thay đổi cấu trúc hay mòn men răng. Gel bảo vệ nướu chuyên sâu ngăn cách 100% không làm rát môi lợi.',
            'effects' => [
                'Tác dụng bẻ gãy các liên kết sắc tố vàng ố do trà, cà phê, rượu vang, hút thuốc lá hoặc yếu tố tuổi tác.',
                'Giúp răng trắng sáng bật từ 3 đến 5 tông màu chỉ sau một liệu trình 45 phút duy nhất.',
                'Công nghệ ánh sáng lạnh không sinh nhiệt, kết hợp khoáng chất bù Fluoride tái khoáng hóa men răng săn chắc.',
                'Đem lại nụ cười rạng rỡ, trẻ trung hơn 5-10 tuổi và tự tin tối đa khi giao tiếp.'
            ],
            'target_audience' => 'Người có hàm răng bị ố vàng, xỉn màu do thực phẩm, thuốc lá, phụ nữ và nam giới muốn chuẩn bị nụ cười tỏa sáng trước sự kiện quan trọng (cưới hỏi, phỏng vấn, lễ tết).',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Thăm khám & Đối chiếu bảng so màu răng Vita',
                    'desc' => 'Bác sĩ kiểm tra men răng, độ nhạy cảm và chụp ảnh màu răng hiện tại để đo lường mức độ trắng sáng sau thực hiện.'
                ],
                [
                    'step' => 2,
                    'title' => 'Cạo vôi răng & Đánh bóng làm sạch bề mặt men',
                    'desc' => 'Vệ sinh sạch mảng bám để gel tẩy trắng có thể tiếp xúc đồng đều 100% lên bề mặt các răng.'
                ],
                [
                    'step' => 3,
                    'title' => 'Cách ly nướu & Thoa gel dưỡng ẩm môi',
                    'desc' => 'Đeo banh miệng mềm mại, phủ lớp màng nhựa sinh học cách ly toàn bộ nướu và chân răng để tránh kích ứng.'
                ],
                [
                    'step' => 4,
                    'title' => 'Thoa gel tẩy trắng & Chiếu ánh sáng Laser (3 hiệp x 15 phút)',
                    'desc' => 'Quét gel tẩy trắng chuẩn nồng độ, kích hoạt bằng đèn Laser Whitening ánh sáng lạnh, lặp lại 2-3 chu kỳ để đạt độ sáng hoàn mỹ.'
                ],
                [
                    'step' => 5,
                    'title' => 'Thoa gel khoáng bù nước & Đo kết quả màu răng mới',
                    'desc' => 'Lau sạch gel, thoa chất chống ê buốt bù khoáng, đối chiếu thước màu để bệnh nhân thấy rõ kết quả bật tông rõ rệt.'
                ]
            ],
            'post_care' => 'Trong 48 giờ đầu: Tránh ăn uống thực phẩm có màu đậm (cà phê, trà, sốt cà chua, tương ớt, socola, nước tương). Uống nhiều nước lọc và dùng ống hút.',
            'doctor_specialist' => 'BS. Phạm Thùy Linh (Chuyên gia Nha khoa Thẩm mỹ, 7 năm kinh nghiệm)',
            'faqs' => [
                ['q' => 'Tẩy trắng răng duy trì được bao lâu?', 'a' => 'Hiệu quả trắng sáng duy trì từ 2 - 3 năm hoặc lâu hơn nếu bạn duy trì chế độ chăm sóc răng miệng tốt và hạn chế thực phẩm gây ố màu.'],
                ['q' => 'Tẩy trắng răng có làm hỏng men răng không?', 'a' => 'Không! Thuốc tẩy trắng chính hãng sử dụng tại Premier Dental được Hiệp hội Nha khoa Hoa Kỳ (ADA) chứng nhận an toàn, chỉ tác động vào sắc tố màu chứ không làm mòn hay tiêu men răng.']
            ]
        ],

        7 => [
            'id' => 7,
            'name' => 'Bọc răng sứ thẩm mỹ',
            'category' => 'Nha Khoa Thẩm Mỹ & Phục Hình',
            'tagline' => 'Răng toàn sứ chính hãng Katana, Cercon HT, Emax – Thiết kế nụ cười Smile Design tỷ lệ vàng',
            'price_from' => 2000000,
            'price_formatted' => '2.000.000đ - 6.500.000đ / răng',
            'duration' => '2 buổi hẹn (cách nhau 2 - 3 ngày)',
            'pain_level' => 'Gây tê bôi & tiêm vi điểm không đau, mài bảo tồn tối đa răng thật',
            'warranty' => 'Bảo hành chính hãng 10 - 15 năm có mã thẻ truy xuất nguồn gốc',
            'image' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=800&auto=format&fit=crop&q=80',
            'technology' => 'Công nghệ chế tác tự động CAD/CAM 3D độ chính xác 5 Micron, Phần mềm thiết kế nụ cười kỹ thuật số Digital Smile Design (DSD), Lò nung sứ nhiệt độ cao Dekema (Đức)',
            'how_it_works' => 'Bác sĩ sửa soạn nhẹ một lớp men răng bên ngoài với tỷ lệ tối thiểu, bảo tồn buồng tủy nguyên vẹn. Dùng máy scan 3D quét chính xác dấu hàm truyền dữ liệu về phòng Lab. Kỹ thuật viên tiện phôi sứ nguyên khối Katana/Cercon bằng máy CNC tự động và đắp lớp sứ bóng đa tầng. Mão sứ sau đó được gắn chặt vĩnh viễn vào thân răng bằng xi măng sinh học Resin siêu dính.',
            'effects' => [
                'Tác dụng phục hồi toàn diện cho răng bị mẻ vỡ lớn, răng thưa, lệch lạc nhẹ hoặc xỉn màu kháng sinh Tetracycline không tẩy trắng được.',
                'Độ cứng chắc chịu lực lên tới 1.200 - 1.400 MPa (gấp 4 đến 5 lần răng thật tự nhiên), thoải mái ăn nhai đồ dai cứng.',
                'Màu sắc trong bóng tự nhiên, vân răng sống động, không đục, không bị đen viền nướu sau nhiều năm sử dụng.',
                'Bảo vệ răng thật bên trong khỏi sự tấn công của vi khuẩn sâu răng và axit môi trường miệng.'
            ],
            'target_audience' => 'Người có răng sâu lớn, răng gãy mẻ, răng ố màu nặng, răng thưa kẽ, răng đã điều trị tủy hoặc muốn cải thiện toàn diện đường cười thẩm mỹ chuẩn phong thủy.',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Khám tổng quát, Chụp ảnh nụ cười & Thiết kế DSD',
                    'desc' => 'Phân tích đường cười, tỷ lệ môi - cung răng - khuôn mặt trên phần mềm Smile Design và cho khách hàng xem trước nụ cười tương lai.'
                ],
                [
                    'step' => 2,
                    'title' => 'Sửa soạn cùi răng nhẹ nhàng & Lấy dấu kỹ thuật số',
                    'desc' => 'Gây tê không đau, sửa soạn men răng với tỷ lệ chuẩn xác bảo tồn tủy, dùng máy quét 3D iTero lấy dấu trong 2 phút.'
                ],
                [
                    'step' => 3,
                    'title' => 'Gắn răng tạm thẩm mỹ bảo vệ trong khi chờ sứ',
                    'desc' => 'Lắp răng tạm thẩm mỹ bằng Composite để bệnh nhân thoải mái giao tiếp và ăn nhai bình thường trong 48 giờ chờ đúc mão sứ.'
                ],
                [
                    'step' => 4,
                    'title' => 'Chế tác mão răng toàn sứ bằng công nghệ CAD/CAM',
                    'desc' => 'Robot tiện phôi sứ Zirconia/Cercon chính hãng từ Đức, nung kết tinh 8 tiếng và đắp sứ bóng thẩm mỹ theo màu răng đã chọn.'
                ],
                [
                    'step' => 5,
                    'title' => 'Thử dáng răng, Kiểm tra khớp cắn & Gắn cố định',
                    'desc' => 'Khách hàng soi gương ưng ý form răng và màu sắc, bác sĩ dùng xi măng sinh học cố định vĩnh viễn và cấp thẻ bảo hành điện tử chính hãng.'
                ]
            ],
            'post_care' => 'Ăn nhai nhẹ nhàng trong 24h đầu để xi măng đạt độ kết dính tối đa. Đánh răng, dùng chỉ nha khoa bình thường và tái khám kiểm tra định kỳ 6 tháng.',
            'doctor_specialist' => 'BS. Phạm Thùy Linh (Chuyên gia Phục hình răng sứ thẩm mỹ, tu nghiệp Đức)',
            'faqs' => [
                ['q' => 'Bọc răng sứ có bị hôi miệng hoặc thâm đen viền lợi không?', 'a' => 'Tuyệt đối không nếu sử dụng dòng răng toàn sứ (như Katana, Cercon, Emax) tại Premier Dental. Khác với răng sứ kim loại giá rẻ, răng toàn sứ 100% trơ sinh học trong môi trường miệng, không oxy hóa, không đen viền lợi và không gây mùi.'],
                ['q' => 'Bọc răng sứ có phải diệt tủy răng không?', 'a' => 'Tiêu chí hàng đầu tại Premier Dental là BẢO TỒN RĂNG THẬT VÀ TỦY RĂNG. Hơn 95% các ca bọc sứ thẩm mỹ được thực hiện giữ tủy răng sống khỏe mạnh hoàn toàn.']
            ]
        ],

        8 => [
            'id' => 8,
            'name' => 'Dán sứ Veneer',
            'category' => 'Nha Khoa Thẩm Mỹ & Phục Hình',
            'tagline' => 'Mặt dán sứ siêu mỏng 0.2 - 0.5mm, bảo tồn tối đa 100% răng thật, thẩm mỹ đỉnh cao',
            'price_from' => 4000000,
            'price_formatted' => '4.000.000đ - 7.500.000đ / răng',
            'duration' => '2 buổi hẹn',
            'pain_level' => 'Hoàn toàn không đau, không ê buốt, chỉ mài nhám siêu mỏng mặt men',
            'warranty' => 'Bảo hành chính hãng 10 - 15 năm',
            'image' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=800&auto=format&fit=crop&q=80',
            'technology' => 'Sứ thủy tinh Lithium Disilicate Emax Press siêu mỏng, Kính hiển vi phẫu thuật nha khoa phóng đại quang học 20X',
            'how_it_works' => 'Bác sĩ chỉ làm nhám một lớp cực mỏng từ 0.2mm - 0.5mm trên bề mặt ngoài của men răng (không mài chu vi như bọc sứ). Mặt dán sứ Emax siêu tinh xảo được gắn chặt vào mặt ngoài của răng bằng kỹ thuật dán dính quang học Resin Bond cao cấp, hòa làm một với men răng thật.',
            'effects' => [
                'Tác dụng bảo tồn tối đa 100% răng thật và mô tủy, không xâm lấn mặt trong hay mặt nhai của răng.',
                'Khắc phục hoàn toàn răng xỉn màu nhẹ, răng thưa kẽ nhẹ, răng không đều dáng hoặc rìa cắn mẻ răng cửa.',
                'Độ thấu quang và tương phản khúc xạ ánh sáng sống động tự nhiên như ngọc trai, mang lại nụ cười sang trọng, quý phái.',
                'Mặt sứ siêu láng bóng chống bám bẩn cà phê và thuốc lá vượt trội.'
            ],
            'target_audience' => 'Khách hàng có cung răng tương đối đều đặn nhưng muốn cải thiện màu sắc trắng trong, người có răng cửa thưa nhẹ, răng mẻ nhẹ hoặc mặt men răng sần sùi kém sắc.',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Thăm khám, Chụp ảnh cung cười & Làm Mock-up thử dáng',
                    'desc' => 'Thiết kế dáng răng tương thích với khuôn mặt, đắp thử lớp răng tạm Mock-up trực tiếp trên miệng để khách hàng trải nghiệm thực tế trước khi làm.'
                ],
                [
                    'step' => 2,
                    'title' => 'Xử lý vi thể bề mặt men răng dưới kính hiển vi',
                    'desc' => 'Chỉ mài nhám siêu mỏng 0.3mm bề mặt ngoài của men răng, hoàn toàn không chạm đến ngà răng hay tủy răng.'
                ],
                [
                    'step' => 3,
                    'title' => 'Scan 3D dấu hàm vi điểm & Chế tác mặt dán sứ',
                    'desc' => 'Kỹ thuật viên ép phôi sứ Emax Press thủy tinh đạt độ chịu lực cao và tạo hiệu ứng loang màu chuyển tiếp tinh xảo như răng thật.'
                ],
                [
                    'step' => 4,
                    'title' => 'Thử màu dán thử (Try-in gel) & Tinh chỉnh',
                    'desc' => 'Dùng gel thử màu chuyên dụng để căn chỉnh độ ấm, độ sáng của nụ cười dưới ánh sáng tự nhiên cho đến khi khách hàng hoàn toàn hài lòng.'
                ],
                [
                    'step' => 5,
                    'title' => 'Dán dính vĩnh viễn bằng công nghệ quang học & Đánh bóng',
                    'desc' => 'Chiếu đèn quang trùng hợp liên kết chặt chẽ mặt dán sứ với men răng, vệ sinh sạch keo thừa và cấp thẻ bảo hành chính hãng.'
                ]
            ],
            'post_care' => 'Chăm sóc và đánh răng như răng thật. Không dùng răng cửa để xé bao bì, cắn đồ quá cứng.',
            'doctor_specialist' => 'BS. Phạm Thùy Linh (Chuyên gia Dán sứ Veneer thẩm mỹ)',
            'faqs' => [
                ['q' => 'Dán sứ Veneer có dễ bị bong tróc khi cắn nhai không?', 'a' => 'Hoàn toàn không! Nhờ chất keo dán sinh học chuyên sâu tạo liên kết hóa học trực tiếp với men răng, mặt sứ Veneer bám dính cực kỳ chắc chắn, độ bền liên kết tương đương men răng tự nhiên.'],
                ['q' => 'Tôi nên chọn Bọc răng sứ hay Dán sứ Veneer?', 'a' => 'Nếu răng bạn tương đối đều và chỉ muốn nụ cười trắng đẹp tự nhiên, Dán sứ Veneer là lựa chọn số 1 để bảo tồn răng thật. Nếu răng bạn mẻ vỡ lớn, thưa nhiều hoặc lệch lạc nặng, Bọc răng sứ sẽ mang lại độ che phủ và phục hồi form tốt hơn.']
            ]
        ],

        9 => [
            'id' => 9,
            'name' => 'Niềng răng / Chỉnh nha',
            'category' => 'Chỉnh Nha & Cấy Ghép',
            'tagline' => 'Công nghệ Chỉnh nha Kỹ thuật số 3D – Mắc cài thông minh & Khay trong suốt Invisalign',
            'price_from' => 20000000,
            'price_formatted' => '20.000.000đ - 85.000.000đ (Trả góp 0%)',
            'duration' => '12 - 24 tháng (Tái khám 4 - 6 tuần/lần)',
            'pain_level' => 'Ê căng nhẹ trong 2-3 ngày đầu khi mới siết dây/đổi khay, sau đó quen dần rất êm',
            'warranty' => 'Cam kết chuẩn phác đồ Clincheck bằng văn bản pháp lý + Tặng hàm duy trì sau niềng',
            'image' => 'https://images.unsplash.com/photo-1598256989800-fe5f95da9787?w=800&auto=format&fit=crop&q=80',
            'technology' => 'Máy scan 3D trong miệng iTero Element 5D tích hợp trí tuệ nhân tạo AI mô phỏng nụ cười tương lai Clincheck trong 60 giây',
            'how_it_works' => 'Bác sĩ ứng dụng lực cơ học sinh học nhẹ nhàng, liên tục từ hệ thống dây cung mắc cài hoặc các khay niềng trong suốt bằng vật liệu SmartTrack độc quyền. Lực kéo tác động có kiểm soát giúp chân răng dịch chuyển từ từ trong xương hàm về đúng vị trí chuẩn sinh lý, tái tạo lại khớp cắn hài hòa.',
            'effects' => [
                'Tác dụng điều chỉnh triệt để các khuyết điểm: răng hô vẩu, răng móm chìa, răng khấp khểnh chen chúc, răng thưa kẽ lớn.',
                'Đưa hai hàm về đúng khớp cắn chuẩn sinh lý, cải thiện rõ rệt khả năng nghiền nát thức ăn, bảo vệ dạ dày tiêu hóa.',
                'Thay đổi tích cực góc nghiêng khuôn mặt (cằm thon gọn hơn, môi bớt nhô, nụ cười thanh thoát).',
                'Giảm thiểu tối đa nguy cơ mòn vẹt men răng do sai lệch khớp cắn, giúp dễ dàng vệ sinh răng miệng phòng ngừa viêm nha chu trọn đời.'
            ],
            'target_audience' => 'Trẻ em từ 7 tuổi (tiền chỉnh nha), thanh thiếu niên và người trưởng thành (20 - 45 tuổi) muốn sở hữu hàm răng đều đẹp tự nhiên mà không cần mài răng.',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Khám lâm sàng, Chụp phim sọ nghiêng Cephalo & CT 3D',
                    'desc' => 'Khảo sát cấu trúc xương sọ mặt, góc hàm, tương quan hàm trên và hàm dưới để tìm nguyên nhân hô móm do răng hay do xương.'
                ],
                [
                    'step' => 2,
                    'title' => 'Scan 3D iTero 5D & Xem trước mô phỏng nụ cười Clincheck',
                    'desc' => 'Bác sĩ quét dữ liệu 3D, lập phác đồ dịch chuyển răng theo từng tuần và cho khách hàng xem trước từng giai đoạn dịch chuyển.'
                ],
                [
                    'step' => 3,
                    'title' => 'Vệ sinh, điều trị tổng quát tiền chỉnh nha',
                    'desc' => 'Cạo vôi răng, xử lý triệt để răng sâu hoặc nhổ răng chỉnh nha (nếu có chỉ định) để tạo khoảng trống dịch chuyển răng thuận lợi.'
                ],
                [
                    'step' => 4,
                    'title' => 'Gắn mắc cài chuẩn xác hoặc bàn giao bộ khay Invisalign',
                    'desc' => 'Bác sĩ gắn khí cụ tỉ mỉ bằng keo sinh học hoặc hướng dẫn đeo khay niềng trong suốt 20-22h mỗi ngày.'
                ],
                [
                    'step' => 5,
                    'title' => 'Tái khám định kỳ theo dõi tiến độ & Tháo niềng đeo hàm duy trì',
                    'desc' => 'Tái khám mỗi 4-6 tuần để thay dây cung/nhận khay mới. Khi đạt khớp cắn lý tưởng, tháo niềng và đeo hàm duy trì cố định nụ cười đẹp bền vững.'
                ]
            ],
            'post_care' => 'Vệ sinh bằng bàn chải kẽ và máy tăm nước. Ăn thức ăn cắt nhỏ mềm, tránh đồ quá dính hoặc quá cứng làm bong mắc cài. Đeo hàm duy trì đều đặn sau tháo niềng.',
            'doctor_specialist' => 'BS. CKI Trần Thị Mai (Chuyên gia Chỉnh nha Invisalign, hơn 1.000 ca thành công)',
            'faqs' => [
                ['q' => 'Bao nhiêu tuổi thì không niềng răng được nữa?', 'a' => 'Chỉnh nha không giới hạn tuổi! Miễn là nền xương hàm và nướu của bạn còn khỏe mạnh, từ 20 đến trên 40 tuổi vẫn niềng răng đạt kết quả hoàn mỹ như ý.'],
                ['q' => 'Chính sách trả góp 0% tại Premier Dental như thế nào?', 'a' => 'Chỉ cần thanh toán trước khoảng 30% chi phí ban đầu, số tiền còn lại được chia đều trả góp linh hoạt mỗi tháng chỉ từ 1.000.000đ - 1.500.000đ không mất lãi suất.']
            ]
        ],

        10 => [
            'id' => 10,
            'name' => 'Cấy ghép Implant Nha Khoa',
            'category' => 'Chỉnh Nha & Cấy Ghép',
            'tagline' => 'Phục hồi răng đã mất hoàn hảo cả chân và thân răng – Trụ Titanium Thụy Sĩ/Mỹ/Hàn Quốc',
            'price_from' => 12000000,
            'price_formatted' => '12.000.000đ - 28.000.000đ / trụ (Tặng răng sứ)',
            'duration' => 'Phẫu thuật đặt trụ 20 - 30 phút, tích hợp xương 3 - 8 tuần',
            'pain_level' => 'Gây tê chuyên sâu không đau, sau cấy ghép êm ái hơn cả nhổ răng thường',
            'warranty' => 'Bảo hành TRỌN ĐỜI đối với trụ Implant chính hãng có mã quét QR quốc tế',
            'image' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=800&auto=format&fit=crop&q=80',
            'technology' => 'Máng hướng dẫn phẫu thuật kỹ thuật số 3D Guide phẫu thuật không rạch vạt nướu, Trụ Titanium nguyên chất xử lý bề mặt sinh học SLA/SLActive tích hợp xương thần tốc',
            'how_it_works' => 'Bác sĩ đặt một trụ Titanium siêu tinh khiết đóng vai trò như chân răng thật trực tiếp vào xương hàm tại vị trí mất răng thông qua máng hướng dẫn định vị chuẩn xác từng 0.1mm. Tế bào xương hàm tự nhiên sẽ bám dính và phát triển quanh trụ. Sau khi tích hợp vững chắc, một khớp nối Abutment và một mão răng toàn sứ được gắn lên trên, hoàn thiện chiếc răng mới 100% như răng thật sinh lý.',
            'effects' => [
                'Tác dụng phục hồi toàn diện cả chân răng và thân răng đã mất, ăn nhai chắc chắn 100% như răng thật tự nhiên.',
                'Ngăn chặn vĩnh viễn hiện tượng tiêu xương hàm, tụt nướu và hóp má lão hóa sớm do mất răng lâu năm gây ra.',
                'Độc lập hoàn toàn, không cần mài mòn 2 chiếc răng thật khỏe mạnh bên cạnh như phương pháp làm cầu răng sứ truyền thống.',
                'Độ bền sử dụng vĩnh viễn trọn đời nếu được chăm sóc vệ sinh đúng cách.'
            ],
            'target_audience' => 'Bệnh nhân bị mất 1 răng, nhiều răng xen kẽ hoặc mất răng toàn hàm do tuổi tác, tai nạn, sâu hỏng nặng muốn có lại hàm răng chắc khỏe vĩnh viễn.',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Chụp phim CT Cone Beam 3D & Xét nghiệm máu tổng quát',
                    'desc' => 'Đo lường thể tích, chiều cao và mật độ xương hàm, kiểm tra sức khỏe tổng quát đảm bảo điều kiện phẫu thuật an toàn tuyệt đối.'
                ],
                [
                    'step' => 2,
                    'title' => 'Lập kế hoạch 3D & In máng định vị phẫu thuật',
                    'desc' => 'Phần mềm mô phỏng hướng cấy ghép, độ sâu và chọn kích thước trụ Implant phù hợp nhất với cơ địa của bệnh nhân.'
                ],
                [
                    'step' => 3,
                    'title' => 'Cấy ghép trụ Implant vào xương hàm (20 phút)',
                    'desc' => 'Gây tê vi điểm không đau, bác sĩ đặt trụ Titanium qua máng định vị cực kỳ êm ái, vết thương siêu nhỏ hạn chế chảy máu.'
                ],
                [
                    'step' => 4,
                    'title' => 'Lắp răng tạm và theo dõi giai đoạn tích hợp xương',
                    'desc' => 'Gắn răng tạm để bảo đảm tính thẩm mỹ khi giao tiếp, xương hàm phát triển bám chặt vào trụ trong 3 - 6 tuần.'
                ],
                [
                    'step' => 5,
                    'title' => 'Lấy dấu mão răng toàn sứ & Gắn răng vĩnh viễn',
                    'desc' => 'Quét dấu kỹ thuật số, chế tác răng sứ hoàn chỉnh gắn lên trụ Implant và cấp thẻ bảo hành quốc tế trọn đời.'
                ]
            ],
            'post_care' => 'Chườm đá lạnh ngoài má ngày đầu, uống thuốc theo toa bác sĩ. Ăn thức ăn mềm, tránh nhai đồ quá dai tại vị trí mới cấy trong 2 tuần đầu, không hút thuốc lá trong quá trình tích hợp xương.',
            'doctor_specialist' => 'ThS.BS Lê Hoàng Nam (Chuyên gia Cấy ghép Implant Thụy Sĩ, 15 năm kinh nghiệm)',
            'faqs' => [
                ['q' => 'Trồng răng Implant có đau không?', 'a' => 'Hầu hết bệnh nhân đều bất ngờ vì cấy Implant nhẹ nhàng hơn nhiều so với nhổ răng! Nhờ có máng định vị phẫu thuật 3D không cần rạch vạt nướu rộng và hệ thống gây tê hiện đại, quá trình cấy diễn ra nhanh gọn chỉ 15-20 phút mà không cảm thấy đau.'],
                ['q' => 'Người cao tuổi hoặc bị tiểu đường có cấy Implant được không?', 'a' => 'Hoàn toàn được! Premier Dental tiếp nhận và điều trị thành công cho nhiều bệnh nhân trên 70 tuổi và người có bệnh nền. Bác sĩ sẽ kiểm tra các chỉ số đường huyết, tim mạch và có phác đồ phối hợp an toàn tuyệt đối trước khi thực hiện.']
            ]
        ]
    ];
}

function get_service_detail_by_id($id) {
    $all = get_all_services_details();
    return $all[(int)$id] ?? null;
}
