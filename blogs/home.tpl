<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="/modules/jmarketplace/views/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="https://limited.developpement.top/modules/jmarketplace/views/js/calltinymce.js">
</script>


<li class="col-xs-6 col-sm-4 col-md-3">
    <a class="" id="returns-link" href="javascript:void(0);" onclick="togglePopup()">
        <span class="link-item">
            <svg width="15" height="15" viewBox="0 0 27 19" fill="red" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M8.03125 1.84375H25.5313M8.03125 9.5H25.5313M8.03125 17.1563H25.5313M1.46875 1.84375H1.47896V1.85542H1.46875V1.84375ZM2.01563 1.84375C2.01563 1.98879 1.95801 2.12789 1.85545 2.23045C1.75289 2.33301 1.61379 2.39063 1.46875 2.39063C1.32371 2.39063 1.18461 2.33301 1.08205 2.23045C0.979492 2.12789 0.921875 1.98879 0.921875 1.84375C0.921875 1.69871 0.979492 1.55961 1.08205 1.45705C1.18461 1.35449 1.32371 1.29688 1.46875 1.29688C1.61379 1.29688 1.75289 1.35449 1.85545 1.45705C1.95801 1.55961 2.01563 1.69871 2.01563 1.84375ZM1.46875 9.5H1.47896V9.51167H1.46875V9.5ZM2.01563 9.5C2.01563 9.64504 1.95801 9.78414 1.85545 9.8867C1.75289 9.98926 1.61379 10.0469 1.46875 10.0469C1.32371 10.0469 1.18461 9.98926 1.08205 9.8867C0.979492 9.78414 0.921875 9.64504 0.921875 9.5C0.921875 9.35496 0.979492 9.21586 1.08205 9.1133C1.18461 9.01074 1.32371 8.95313 1.46875 8.95313C1.61379 8.95313 1.75289 9.01074 1.85545 9.1133C1.95801 9.21586 2.01563 9.35496 2.01563 9.5ZM1.46875 17.1563H1.47896V17.1679H1.46875V17.1563ZM2.01563 17.1563C2.01563 17.3013 1.95801 17.4404 1.85545 17.5429C1.75289 17.6455 1.61379 17.7031 1.46875 17.7031C1.32371 17.7031 1.18461 17.6455 1.08205 17.5429C0.979492 17.4404 0.921875 17.3013 0.921875 17.1563C0.921875 17.0112 0.979492 16.8721 1.08205 16.7696C1.18461 16.667 1.32371 16.6094 1.46875 16.6094C1.61379 16.6094 1.75289 16.667 1.85545 16.7696C1.95801 16.8721 2.01563 17.0112 2.01563 17.1563Z"
                    stroke="red" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {l s='Conditions générale de vente' d='Shop.Theme.Customeraccount'}
        </span>
    </a>
</li>


<div class="pop-up-condition" id="popup">
    <div class="pop-up-content">
        <span class="close" onclick="togglePopup()">&times;</span>
        <form class="" action="" method="post" id="conditions">
            <h2>Ajouter Conditions générales de vente</h2>
            <div class="alert alert-success" id="success" style="display: none;"></div>

            <div class="form-group">
                <textarea type="text" id="mce_3" class="form-control"
                    name="condition_seller">{{$condition.condition nofilter}}</textarea>
            </div>
            <div>
                <button type="submit" class="btn btn-primary btn_add_condition">
                    <span>Ajouter</span>
                    <div class="spinner-container">
                        <svg class="spinner" viewBox="0 0 100 101" fill="blue" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289"
                                fill="currentFill" />
                        </svg>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .pop-up-condition {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        color: white;
    }

    .btn_add_condition {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        transition: .3s cubic-bezier(.25, .8, .25, 1);
    }

    .pop-up-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        color: black;
        width: 70%;
    }

    .spinner-container {
        display: none;
        width: 30px;
        height: 30px;
    }

    .spinner-container.active {
        display: inline-block;
    }

    .spinner-container svg {
        color: transparent;
        fill: white;
    }

    .spinner {
        width: 100%;
        height: 100%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    function togglePopup() {
        var popup = $('#popup');
        if (popup.css('display') === 'none' || popup.css('display') === '') {
            popup.css('display', 'flex');
        } else {
            popup.css('display', 'none');
        }
    }


    $(document).ready(function() {
        $('form#conditions').submit(function(e) {
            e.preventDefault();
            var conditionSellerContent = tinyMCE.get('mce_3').getContent();
            var btn = $('.btn_add_condition');
            btn.attr("disable", "true")
            btn.find('.spinner-container').addClass('active');

            var formData = $(this).serializeArray();
            $.ajax({
                url: urlCondition,
                type: 'POST',
                data: $.param(formData),
                success: function(response) {
                    console.log('Success:', response);
                    btn.attr("disable", "false")
                    btn.find('.spinner-container').removeClass('active');
                    $('#success').css('display', 'block');
                    var jsonResponse = JSON.parse(response);
                    $('#success').text(jsonResponse.message);
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });

        });
    });
</script>