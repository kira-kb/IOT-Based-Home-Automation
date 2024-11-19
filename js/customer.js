'use strict';

const submit = document.querySelector(".submit");
const modal = document.querySelector('.modal');
const overlay = document.querySelector('.overlay');
const modalCloseBtn = document.querySelector('.btn--close-modal');

const forgetPasswordForm = document.querySelector('form');
const updateBtn = document.querySelector('.updt').querySelector('span')
const password = document.querySelector('.p')
const confirmPassword = document.querySelector('.cp')

const notifications = document.querySelector('.notifications');

//////////////////////


const day_night = document.querySelector('#day_night');
const headers = document.querySelector('header')
const lis = document.querySelectorAll('li')
const ceils = document.querySelectorAll('.ceil');

const switchAccount = document.querySelector('.switchAccount');
const logout = document.querySelector('.logout');

///////////////////////

const sendFeedback = document.querySelector('.sndfdb');

const feedModal = document.querySelector('.feedModal');
const feedForm = document.querySelector('.modal__form2');

const feedModalCloseBtn = document.querySelector('.btn--close-modal2');

const buttons = document.querySelectorAll('.btn');
const leds = [0, 0, 0, 0, 0, 0];

const checkButton = (index) => {
    if (leds[index]) {
        buttons[index].classList.add('active');
    } else {
        buttons[index].classList.remove('active');
    }
};

const upload = async () => {
    let serverData = [];
    leds.forEach((led, i) => serverData.push(`led${i + 1}=${led}`))

    try {
        const response = await fetch("customer.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: serverData.join('&') + "&updateData='updateData'",
        });

        // console.log(await response.text());
    } catch (error) {
        console.error("Error during upload:", error);
    }
};

const updateLEDs = (data) => {
    leds[0] = +data.led1;
    leds[1] = +data.led2;
    leds[2] = +data.led3;
    leds[3] = +data.led4;
    leds[4] = +data.led5;
    leds[5] = +data.led6;

    for (let i = 0; i < buttons.length; i++) {
        checkButton(i);
    }
};

const getStatus = async () => {
    try {
        const response = await fetch("customer.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: 'getData="getData"'
        });
        // const data = await response.text();
        // console.log(data);
        const data = await response.json();
        updateLEDs(data);
    } catch (error) {
        console.error("Error getting status:", error);
    }
};

getStatus();
setInterval(getStatus, 100);

buttons.forEach((btn, i) => btn.addEventListener('click', () => {
    leds[i] = !leds[i];
    checkButton(i);
    upload();
}))

sendFeedback.addEventListener('click', ()=> {
    overlay.classList.remove('hidden');
    feedModal.classList.remove('hidden');
})

day_night.addEventListener('click', () => {
    if (day_night.checked) {
        console.log('checked')
        document.body.style.backgroundColor = 'aliceblue';
        document.body.style.color = '#333';

        headers.style.backgroundColor = '#ddd'
        headers.style.color = '#343434';

        lis.forEach(li => li.style.backgroundColor = '#f3f3f3')

        ceils.forEach(ceil => {
            ceil.style.backgroundColor = '#eee'
            ceil.style.color = '#333'
            ceil.style.boxShadow = '0 0 30px #ddd'
        })
    } else {
        document.body.style = '';
        headers.style = '';
        lis.forEach(li => li.style = '');
        ceils.forEach(ceil => ceil.style = '')
    }
})


async function request() {
    const res = await fetch('customer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: this
    });

    console.log(this)
    const goTo = await res.json()

    if (goTo.status === 'success') {
        window.location = goTo.location;
    }

}

updateBtn.addEventListener('click', () => {
    overlay.classList.remove('hidden')
    modal.classList.remove('hidden')
})

overlay.addEventListener('click', () => {
    overlay.classList.add('hidden')
    modal.classList.add('hidden')
    feedModal.classList.add('hidden')
})

modalCloseBtn.addEventListener('click', () => {
    overlay.classList.add('hidden')
    modal.classList.add('hidden')
})

feedModalCloseBtn.addEventListener('click', () => {
    overlay.classList.add('hidden')
    feedModal.classList.add('hidden')
})

confirmPassword.addEventListener('input', () => {
    if (confirmPassword.value === password.value) {
        confirmPassword.classList.remove('errInput')
        confirmPassword.classList.add('correctInput')
    }
    else {
        confirmPassword.classList.add('errInput')
        confirmPassword.classList.remove('correctInput')
    }
})

function createToast(type, icon, title, text) {
    let newToast = document.createElement('div');
    newToast.innerHTML = `
        <div class="toast ${type}">
                <i class="${icon}"></i>
                <div class="content">
                    <div class="title">${title}</div>
                    <span>${text}</span>
                </div>
                <i class="close las la-times"
                onclick="(this.parentElement).remove()"
                ></i>
            </div>`;

    notifications.appendChild(newToast);
    newToast.timeOut = setTimeout(() => newToast.remove(), 2000)
}

// const sendForms = 

forgetPasswordForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(forgetPasswordForm);

    // Convert FormData to an object
    const formDataObject = {};
    formData.forEach((value, key) => formDataObject[key] = value);

    // console.log(formDataObject)
    const res = await fetch('customer.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams(formDataObject)
    })
    const data = await res.json();

    if (data.status === 'success') {

        let type = 'success';
        let icon = 'las la-check';
        let title = 'Success';
        let text = data.message;
        createToast(type, icon, title, text);
    } else {

        let type = 'error';
        let icon = 'las la-exclamation';
        let title = 'Error';
        let text = data.message;
        createToast(type, icon, title, text);

    }
})

feedForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(feedForm);

    // Convert FormData to an object
    const formDataObject = {};
    formData.forEach((value, key) => formDataObject[key] = value);

    // console.log(formDataObject)
    const res = await fetch('customer.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams(formDataObject)
    })
    const data = await res.json();

    if (data.status === 'success') {

        let type = 'success';
        let icon = 'las la-check';
        let title = 'Success';
        let text = data.message;
        createToast(type, icon, title, text);
    } else {

        let type = 'error';
        let icon = 'las la-exclamation';
        let title = 'Error';
        let text = data.message;
        createToast(type, icon, title, text);

    }
});

switchAccount.addEventListener('click', request.bind("switchAccount=switchAccount"))
logout.addEventListener('click', request.bind("logout=logout"))
