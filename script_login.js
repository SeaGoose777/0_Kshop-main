    function sign_up() {
      const login = document.querySelector('.login_access');
      const register = document.querySelector('.login_registration');
      
      // Fade out login
      login.style.opacity = '0';
      
      setTimeout(() => {
        login.style.display = 'none';
        // Fade in registration
        register.style.display = 'block';
        setTimeout(() => {
          register.style.opacity = '1';
        }, 50);
      }, 300);
    }

    function back_to_login() {
      const login = document.querySelector('.login_access');
      const register = document.querySelector('.login_registration');
      
      // Fade out registration
      register.style.opacity = '0';
      
      setTimeout(() => {
        register.style.display = 'none';
        // Fade in login
        login.style.display = 'block';
        setTimeout(() => {
          login.style.opacity = '1';
        }, 50);
      }, 300);
    }
