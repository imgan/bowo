<div id="product-header">
  <section class="bg-theme-gradient breadcrumb-area">
    <div class="container">
      <div class="row">
        <div class="col-md-12 breadcrumb-contents">
          <div class="pt-5 pb-5">
            <h2 class="page-title">
              Daftar Akun
            </h2>
          </div>
        </div>
        <!-- end /.col-md-12 -->
      </div>
      <!-- end /.row -->
    </div>
    <!-- end /.container -->
  </section>
</div>
<div class="clearfix"></div>
<div class="mt-5"></div>
<section id="product-detail">
  <div class="container">
    <div class="row">
      
      <div class="col-md-10 col-lg-7 mx-auto">
        <?php $this->load->view('backoffice/partials/_alert');?>
        <div class="bg-white border border-radius-5">
          <div class="p-3">
            <h4>Daftar Akun Gratis</h4>
            <hr/>
            <form action="<?=base_url('auth/doRegister?redirect=' . $this->input->get('redirect'));?>" method="post" id="form">
            <input type="hidden" id="captcha-validate" value="0">
              <div class="form-group">
                <label for="">
                  <strong>Nama Depan</strong>
                </label>
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                <input type="text" onkeypress="return /[a-z]/i.test(event.key)" name="first_name" class="form-control form-search" placeholder="Masukan Nama Depan" required autocomplete="off">
              </div>
              <div class="form-group">
                <label for="">
                  <strong>Nama Belakang</strong>
                </label>
                <input type="text" onkeypress="return /[a-z]/i.test(event.key)" name="last_name" class="form-control form-search" placeholder="Masukan Nama Belakang"  autocomplete="off">
              </div>
              <div class="form-group">
                <label for="">
                  <strong>Nomor Handphone</strong>
                </label>
                <input type="number" name="phone" class="form-control form-search" placeholder="Masukan No. Handphone" required autocomplete="off">
              </div>
              <div class="form-group">
                <label for="">
                  <strong>Email</strong>
                </label>
                <input type="email" name="email" class="form-control form-search" placeholder="Masukan Email" required autocomplete="off">
              </div>
              <div class="form-group">
                <label for="">
                  <strong>Password</strong>
                </label>
                <input type="password" id="password" name="password" class="form-control form-search" placeholder="Masukan Password" required autocomplete="off">
              </div>
              <div class="form-group">
                <label for="">
                  <strong>Ulangi Password</strong>
                </label>
                <input type="password" name="password_confirm" class="form-control form-search" placeholder="Masukan Ulang Password" required autocomplete="off">
              </div>
              <div class="form-group">
                <label for="">
                  <strong>Daftar Sebagai</strong>
                </label>
                <select name="registrasi" id="registrasi" class="form-contrl form-search">
                  <option value="member">Member</option>
                  <option value="affiliate">Affiliator</option>
                </select>
              </div>
              <div class="form-group">
                <div class="slidercaptcha card">
                    <div class="card-header">
                        <span>Verifikasi</span>
                    </div>
                    <div class="card-body mb-5"><div id="captcha"></div></div>
                </div>
              </div>
              <div class="form-group">
                <button disabled id="btn" class="btn btn-info btn-block btn-lg border-radius-none btn-info-gradient">Daftar</button>
              </div>
              <div>
                <span>
                  Sudah punya akun? <a href="<?=base_url();?>auth/login">Masuk</a>
                  atau <a href="">Lupa Password</a>
                </span>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<link href="<?=resource_url();?>css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<link href="<?=resource_url();?>css/slidercaptcha.css" rel="stylesheet" />
<script src="<?=resource_url();?>js/longbow.slidercaptcha.js"></script>
<script>
  let trycaptcha = 0;
  
  $('#captcha').sliderCaptcha({
    width: 250,
    height: 150,
    sliderL: 42,
    sliderR: 9,
    offset: 5, 
    loadingText: 'Loading...',
    failedText: 'Coba Lagi',
    barText: 'Geser untuk Verifikasi',
    repeatIcon: 'fa fa-repeat',
    maxLoadCount: 3,
    localImages: function () { // uses local images instead
      return 'images/Pic' + Math.round(Math.random() * 4) + '.jpg';
    },
    onSuccess:function() {
      trycaptcha++
      $('#captcha-validate').val("1")
      $('#btn').prop("disabled", false)
    },
    onFail: function() {
      trycaptcha++
      $('#captcha-validate').val("0")
      $('#btn').prop("disabled", true)
    }
  });

  if($("#form").length > 0 ) {
    $("#btn-proses-pesanan").removeAttr("disabled");
    $("#form").validate({
      rules: {
        first_name: {
          required: true,
          minlength: 2
        },
        registrasi: {
          required: true,
        },
        last_name: {
          required: true,
          minlength: 2
        },
        email: {
          required: true,
          minlength: 2,
          email: true
        },
        phone: {
          required: true,
          minlength: 10,
          number: true
        },
        password: {
          required: true,
          minlength: 8
        },
        password_confirm: {
          required: true,
          equalTo: "#password"
        }
      },
      errorPlacement: function (error, element) {
        var name = element.attr('name');
        var type = element[0].tagName;
        console.log(type)
        var errorSelector = '.form-control-feedback[for="' + name + '"]';
        var $element = $(errorSelector);
        if ($element.length) {
            $(errorSelector).html(error.html());
        } else {
            if (type == 'SELECT') {
                error.insertAfter(element.next());
            }
            else {
                error.insertAfter(element);
            }
        }
      },
      submitHandler: function(form) {
        if($('#captcha-validate').val() == "1" && trycaptcha > 0) {
          $('#btn').prop("disabled", true)
          form.submit()
        }
      },
    });
  }  
</script>