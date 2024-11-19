const formTemplates = {
    "addDeviceForm": {
        "template": `
    <input type="hidden" name="type" value="addDevice">
        <div class="modal-form-content">
          <span>Device Name: </span
          ><input type="text" name="deviceName" id="" class="modal-form-input" />
        </div>`,

        "identifiyer": `<span class="las la-inbox modal-icon"></span>
        <div class="modal-type">Add Device</div>`,
    },
    "addCustomerForm": {
        "template": `<input type="hidden" name="type" value="sellDevice">
            <div class="modal-form-content">
                <span>Email: </span><input type="email" name="email" id="" class="modal-form-input" />
            </div>
            <div class="modal-form-content">
                <span>Password: </span><input type="text" name="password" id="" class="modal-form-input" />
            </div>

            <div class="modal-form-content">
                <span>Device: </span>
                <input name="deviceName" id="" value="%DEVICENAME%" class="modal-form-input device_name">
            </div>`,
        "identifiyer": `<span class="las la-users modal-icon"></span>
                        <div class="modal-type">Sell Device</div>`
    },
    "updatePriceForm": {
        "template": `<input type="hidden" name="type" value="updatePrice">
    <div class="modal-form-content">
    <span>Amount: </span><input type="number" name="price" id="" class="modal-form-input">
    </div>`,
        "identifiyer": `<span class="las la-money-bill-wave modal-icon"></span>
    <div class="modal-type">Change Current Price</div>`
    },
    "changePasswordForm": {
        "template": `
        <input type="hidden" name="type" value="Change Password">
          <div class="modal-form-content">
            <span>Password: </span
            ><input type="password" name="password" placeholder="*********************" id="p" class="modal-form-input" />
          </div>
          <div class="modal-form-content">
            <span>Confirm Password: </span
            ><input type="password" name="confirmPassword" placeholder="*********************" id="cp" class="modal-form-input" />
          </div>`,
        "identifiyer": `<span class="las la-lock-open modal-icon"></span>
          <div class="modal-type">Change Password</div>`
    }
};

const deviceTable = `<tr>
              <td>#%NUM% </td>
              <td>
                <div class="client">
                  <div class="client-info">
                    <h4> %DEVICE% </h4>
                  </div>
                </div>
              </td>
                <td>$ %PRICE% </td>
              <td>
                <div class="actions">
                    <span title="Suspend" class="las la-shopping-bag">&supsetneqq;</span>
                </div>
              </td>
            </tr>`

const sideMenu = document.querySelector('.side-menu');
const sideMenuLists = sideMenu.querySelectorAll('li');
const sideMenuAnchorLists = sideMenu.querySelectorAll('a');

const main = document.querySelectorAll('main');
const deviceMain = document.querySelector('.Device-main');
const softwareMain = document.querySelector('.Software-main');

const burgerMenu = document.getElementById('menu-toggle');

const modal = document.querySelector('.modal');
const modalForms = document.querySelector('.modal-forms');
const modalCloseBtn = document.querySelector('.x');
const modalIdentifiyer = document.querySelector('.modal-identifiyer');
const overlay = document.querySelector('.overlay');

const howToModal = document.querySelector('.producer_modal')
const howToCloseBtn = document.querySelector('.x2');
const howToInstruction = document.querySelector('.how_to_instruction');

const submitBtn = document.querySelector('.submit-btn');
const form = document.querySelector('form');

const addDeviceButton = document.querySelector('.AddDeviceButton');
const updatePriceBtn = document.querySelector('.update-price');

const changePassword = document.querySelector('.changePassword');

const notifications = document.querySelector('.notifications');

const table = document.querySelector('.deviceTable');

const versionSelect = document.getElementById('version_select')
const versionView = document.getElementById('version_view')
const downloadLink = document.querySelector('.download_link')

const codeRenderPage = document.querySelector('.codeRenderPage')

// #fff logout btn
const logoutBtn = document.querySelector('.logout');

let oldData = {}


// ================================================================//
const render = (incomming, type = false) => {

    const obj = incomming

    if (type === 'device') {

        let deviceTemplate = '';

        obj.devices.forEach((device, i) => {
            deviceTemplate += deviceTable.replace('%NUM%', i + 1).replace('%DEVICE%', device.deviceName)
                .replace('%PRICE%', obj.price)

            table.innerHTML = deviceTemplate;

            const deviceActionsSell = document.querySelectorAll('.la-shopping-bag');

            deviceActionsSell?.forEach((btn) => btn.addEventListener('click', () => {
                const deviceName = btn.parentElement.parentElement.parentElement.querySelector('h4').textContent

                overlay.classList.remove('hidden')
                modal.classList.remove('hidden')

                modalForms.innerHTML = formTemplates.addCustomerForm.template.replace('%DEVICENAME%', deviceName);
                modalIdentifiyer.innerHTML = formTemplates.addCustomerForm.identifiyer
            }))

        })


    }
    else if (type === 'firmwares') {

        versionSelect.innerHTML = ''
        obj.firmwares.forEach(code => {
            const options = document.createElement('option');
            options.textContent = code.code.replace('firmwares/', 'Smartizer SZ-')
            options.value = '../' + code.code

            versionSelect.insertAdjacentElement('beforeend', options)

        })

        versionView.value = versionSelect.value.replace('firmwares/', 'Smartizer SZ-');
        console.log(versionSelect.value);
        downloadLink.href = versionSelect.value.replace('../', '');
        downloadLink.download = versionSelect.value.replace('firmwares/', 'Smartizer SZ-')

        const codeRender = async () => {
            const response = await fetch(`iotAutomate/${versionSelect.value}`);
            const data = await response.text();

            codeRenderPage.textContent = data;

        }

        codeRender()

        versionSelect.addEventListener('input', () => {
            versionView.value = versionSelect.value.replace('../firmwares/', 'Smartizer SZ-');
            downloadLink.href = versionSelect.value.replace('../', '');
            downloadLink.download = versionSelect.value.replace('../firmwares/', 'Smartizer SZ-')
            // console.log(versionSelect.value)
            codeRender()
        })
    }
}

const updator = async () => {
    const response = await fetch('manufacturer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `type=pageInfo`

    });
    const data = await response.json();
    // console.log(JSON.parse(data.message));

    if (data.status === 'success') {

        const newData = JSON.parse(data.message);
        if (Object.keys(oldData).length === 0) {
            oldData = newData;

            render(oldData, 'device')
            return render(oldData, 'firmwares')
        }
        // #f00 check price
        if (oldData.price != newData.price) {
            oldData = newData;

            return render(oldData, 'device')
        }
        // #f00 check devices
        if (oldData.devices.join(',') != newData.devices.join(',')) {
            oldData.devices = newData.devices

            render(oldData, 'device')
        }
        // #f00 check firmwares
        if (oldData.firmwares.join(',') != newData.firmwares.join(',')) {
            oldData.firmwares = newData.firmwares;

            render(oldData, 'firmwares')
        }
    } else {
        console.log('error')
    }
}

updator();
setInterval(updator, 500);

// ================================================================//

const displayChanger = (type) => {
    if (type == 'Firmwares') {
        main.forEach(itm => itm.classList.add('hidden'))
        softwareMain.classList.remove('hidden')
    };

    if (type == 'Devices') {
        main.forEach(itm => itm.classList.add('hidden'))
        deviceMain.classList.remove('hidden')
    };
}

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

sideMenuAnchorLists.forEach(element => {
    element.addEventListener('click', e => e.preventDefault())
});

sideMenuLists.forEach(list => {
    list.addEventListener('click', function () {
        sideMenuLists.forEach(item => {
            item.querySelector('a').classList.remove('activated');
        })
        list.querySelector('a').classList.add('activated');

        // Change Desplay Content
        displayChanger(list.querySelector('small').textContent);

        // #fff responsive design
        if (window.innerWidth < 769) {
            burgerMenu.checked = false;
        }
    })
});


updatePriceBtn.addEventListener('click', () => {
    modalForms.innerHTML = formTemplates.updatePriceForm.template;
    modalIdentifiyer.innerHTML = formTemplates.updatePriceForm.identifiyer;

    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
    howToModal.classList.add('hidden');
});

addDeviceButton.addEventListener('click', () => {
    modalForms.innerHTML = formTemplates.addDeviceForm.template;
    modalIdentifiyer.innerHTML = formTemplates.addDeviceForm.identifiyer;

    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
})

changePassword.addEventListener('click', () => {
    modalForms.innerHTML = formTemplates.changePasswordForm.template;
    modalIdentifiyer.innerHTML = formTemplates.changePasswordForm.identifiyer;

    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
})

overlay.addEventListener('click', () => {
    overlay.classList.add('hidden')
    modal.classList.add('hidden')
    howToModal.classList.add('hidden')
})

modalCloseBtn.addEventListener('click', () => {
    overlay.classList.add('hidden')
    modal.classList.add('hidden')
})

howToCloseBtn.addEventListener('click', () => {
    overlay.classList.add('hidden');
    howToModal.classList.add('hidden');

})

howToInstruction.addEventListener('click', () => {
    overlay.classList.remove('hidden');
    howToModal.classList.remove('hidden');
})
// ================================================================//

const loadAnim = `<div class="load-anim-container"><div class="half"></div><div class="half"></div></div>`;
form.addEventListener('submit', async (e) => {
    e.preventDefault();


    modalForms.insertAdjacentHTML('afterbegin', loadAnim);
    const loadAnimContainer = document.querySelector('.load-anim-container');

    try {

        // Create a new FormData object
        const formData = new FormData(form);

        // Convert FormData to an object
        const formDataObject = {};
        formData.forEach(function (value, key) {
            formDataObject[key] = value;
        });
        // console.log(1);
        // check if there is empty value
        const formHasEmptyValues = Object.values(formDataObject).some(value => value === '');
        if (formHasEmptyValues) {
            loadAnimContainer.remove();
            return new Error;
        }
        // console.log(2)
        const response = await fetch('manufacturer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams(formDataObject)
        });
        const data = await response.json();
        // const data = await response.text();
        // console.log(data)

        // Handle the response from the server

        if (data.status === 'success') {

            loadAnimContainer.remove();

            createToast('success', 'las la-check', 'Success', `👍 ${data.message}`);
        } else if (data.status === 'warning') {

            loadAnimContainer.remove();

            createToast('warning', 'las la-exclamation', 'Error', data.message);

        } else {

            loadAnimContainer.remove();

            createToast('error', 'las la-exclamation', 'Error', data.message);

        }
    } catch {
        loadAnimContainer.remove();

        createToast('error', 'las la-exclamation', 'Error', "Error!💥 faild to connect to the server");
    }
});

// #fff logout function

logoutBtn.addEventListener('click', async () => {
    const response = await fetch('manufacturer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: "logout=logout"
    });
    const data = await response.json();
    console.log(data);
    if (data.message === 'logedout') location.reload();
})

document.querySelector('.switch_account')?.addEventListener('click', () => window.location = 'customer.php')

// code display #fff
window.addEventListener('DOMContentLoaded', (event) => {
    Prism.highlightAll();
});

