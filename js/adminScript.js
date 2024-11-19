import { formTemplates, tableTemplate } from "./Templates.js";

const sideMenu = document.querySelector('.side-menu');
const sideMenuLists = sideMenu.querySelectorAll('li');
const sideMenuAnchorLists = sideMenu.querySelectorAll('a');

const main = document.querySelectorAll('main');
const dashboardMain = document.querySelector('.Dashboard-main');
const deviceMain = document.querySelector('.Device-main');
const employeeMain = document.querySelector('.Employee-main');
const customerMain = document.querySelector('.Customer-main');
const feedbackMain = document.querySelector('.Feedback-main');
const feedbackActions = document.querySelector('.feedback-actions').querySelectorAll('span');
const transactionMain = document.querySelector('.Transaction-main');

const burgerMenu = document.getElementById('menu-toggle');

const modal = document.querySelector('.modal');
const modalForms = document.querySelector('.modal-forms');
const modalCloseBtn = document.querySelector('.x');
const modalIdentifiyer = document.querySelector('.modal-identifiyer');
const overlay = document.querySelector('.overlay');

const submitBtn = document.querySelector('.submit-btn');
const form = document.querySelector('form');

const buttons = document.querySelectorAll('button');
const updatePriceBtn = document.querySelector('.update-price');

const notifications = document.querySelector('.notifications');

const deviceTableView = document.querySelector('.deviceTable');
const customerTableView = document.querySelector('.customerTable');
const employeeTableView = document.querySelector('.employeeTable');
const transactionTableView = document.querySelector('.transactionTable');

const transactionCurrentPrice = document.querySelector('.currentPrice__transaction');
const transactionBallance = document.querySelector('.ballance__transaction');
const transactionEarn = document.querySelector('.earn__transaction');
const transactionLoss = document.querySelector('.loss__transaction');

const feedbackList = document.querySelector('.feedback-list').querySelector('ul');
const feedbackMsg = document.querySelector('.feedback-msg');
// console.log(feedbackList);

const dashboardCurrentPrice = document.querySelector('.dashboard__cp');
const dashboardBallance = document.querySelector('.dashboard__ballance');
const dashboardComments = document.querySelector('.dashboard__comments');
const dashboardCustomers = document.querySelector('.dashboard__customers');
const dashboardDevices = document.querySelector('.dashboard__devices');
const dashboardActiveDevices = document.querySelector('.dashboard__ad');
const dashboardInactiveDevices = document.querySelector('.dashboard__iad');
const dashboardSuspendedDevices = document.querySelector('.dashboard__sd');
const dashboardEmployees = document.querySelector('.dashboard__employees');
const dashboardManufacturrers = document.querySelector('.dashboard__manufacturrers');
const dashboardSellers = document.querySelector('.dashboard__sellers');
const dashboardSoftwareVersion = document.querySelector('.dashboard__version');

// #fff search and filter
const deviceFilter = document.getElementById('deviceFilter');
const deviceSearch = document.getElementById('deviceSearch');

const employeeFilter = document.getElementById('employeeFilter');
const employeeSearch = document.getElementById('employeeSearch');

const customerFilter = document.getElementById('customerFilter');
const customerSearch = document.getElementById('customerSearch');

const transactionFilter = document.getElementById('transactionFilter');
const transactionSearch = document.getElementById('transactionSearch');

const feedbackFilter = document.getElementById("feedback-filter");
const feedbackSearch = document.getElementById("feedback-search");

const changePassword = document.querySelector('.changePassword');
const changePasswordModal = document.querySelector('.modal2');
const changePasswordModalX = document.querySelector('.btn--close-modal');

// #fff logout btn
const logoutBtn = document.querySelector('.logout');

// dashboardComments.textContent = 400;
dashboardSoftwareVersion.textContent = 'Smartizere SZ-1.';

let deviceLists = '';

let oldData = {}

let unknownArray = [{
  "device": 'UNKNOWN',
  "price": 'UNKNOWN',
  "action": 'UNKNOWN',
  "status": 'UNKNOWN',
  "title": 'UNKNOWN',
  "email": 'UNKNOWN',
  "firstName": "UNKNOWN",
  "lastName": "",
  "sallary": "UNKNOWN",
  "profilePic": "./img/default/err_pic.png",
  "role": "UNKNOWN",
}]

// ================================================================//
const pageUpdater = async () => {


  const templateMaker = (data, type) => {


    const suspendActivateDelete = (btn, i, type, incommingData) => {

      const sendToServer = async (item) => {
        const response = await fetch('admin.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `identify=SAD&item=${item}`

        });
        const data = await response.text();
      }

      if (!(type === 'empDelete')) {
        let selectedElement = btn.parentElement.parentElement.parentElement.querySelector('h4').textContent;

        if (type === 'customer') selectedElement = btn.parentElement.parentElement.parentElement.querySelector('small').textContent;
        // console.log(selectedElement)
        // console.log(data[i].device)

        const confirmer = () => confirm('refresh your page something went wrong') ? location.reload() : '';
        if (!(incommingData[i].device === selectedElement.trim())) return confirmer();

        if (btn.getAttribute('title')?.trim() === 'Suspend' || btn.getAttribute('title')?.trim() === 'Activate' || type === 'delete') {
          return sendToServer(incommingData[i].device)
        }

        if (type === 'sell' && btn.getAttribute('title')?.trim() === 'Sell') {
          modalForms.innerHTML = formTemplates.addCustomerForm.template.replace('%DEVICELIST%', `<option value="${incommingData[i].device}">${incommingData[i].device}</option>`);
          modalIdentifiyer.innerHTML = formTemplates.addCustomerForm.identifiyer;

          overlay.classList.remove('hidden');
          modal.classList.remove('hidden');
        }
      }

      if (type === 'empDelete') {
        const btnItem = btn.getAttribute('id')
        sendToServer(btnItem + '&type=emp')
      }

    }

    if (type === 'devices') {

      const render = (renderArray) => {
        let deviceTemplate = '';

        renderArray.forEach((device, i) => {
          deviceTemplate += tableTemplate.deviceTable.replace('%NUM%', i + 1).replace('%DEVICE%', device.device)
            .replace('%PRICE%', device.price).replace('%TITLE%', device.title)
            .replace('%ACTION%', device.action).replaceAll('%STATUS%', device.status)
            .replace('%BUYYER%', device.title === 'Sell' ? '' : device.email)
            // .replace('%DELETE%', device.action === 'shopping-bag' ? '<span class="las la-trash device_action_delete"></span>' : '')
            // .replace('%DELETE%', '<span class="las la-trash device_action_delete"></span>')

          deviceTableView.innerHTML = deviceTemplate;

          const deviceActionsSell = document.querySelectorAll('.device_action_sell');
          const deviceActionsDelete = document.querySelectorAll('.device_action_delete');

          deviceActionsSell?.forEach((btn, i) => btn.addEventListener('click', () => {
            suspendActivateDelete(btn, i, 'sell', renderArray)
          }))

          deviceActionsDelete?.forEach((btn, i) => btn.addEventListener('click', () => {
            const difference = (deviceActionsSell.length - deviceActionsDelete.length) < 0 ? 0 : (deviceActionsSell.length - deviceActionsDelete.length);
            suspendActivateDelete(btn, i + difference, 'delete', renderArray)
          }))
        })
      }


      // #F00 filter
      let filterdData = data;
      let searchData;

      const search = () => {
        if (deviceSearch.value) searchData = filterdData.filter(obj => obj.device?.includes(deviceSearch.value) || obj.email?.includes(deviceSearch.value))
        else searchData = filterdData

        if (searchData.length == 0) searchData = unknownArray

        render(searchData)
      }

      search()

      deviceFilter.addEventListener('change', () => {
        filterdData = data.filter(device => device.status === deviceFilter.value)
        if (filterdData.length == 0) filterdData = data;

        search()
      })

      deviceSearch.addEventListener('input', search)
    }

    else if (type === 'employees') {
      const render = (renderArray) => {
        let employeeTemplate = '';
        renderArray.forEach((employee, i) => {
          employeeTemplate += tableTemplate.employeeTable.replace('%NUM%', i + 1)
            .replace('%PROFILE%', employee.profilePic)
            .replace('%FIRSTNAME%', employee.firstName)
            .replace('%LASTNAME%', employee.lastName)
            .replace('%SALLARY%', `$${employee.sallary}`)
            .replace('%ROLE%', employee.role)
            .replace('%ID%', employee.id)
            .replaceAll('%STATUS%', employee.status)

          employeeTableView.innerHTML = employeeTemplate;

          const employeeActionEdit = document.querySelectorAll('.employee_action_edit');
          const employeeActionsDelete = document.querySelectorAll('.employee_action_delete');

          employeeActionEdit?.forEach((btn, i) => btn.addEventListener('click', () => {

            let employeeEdittorForm = formTemplates.editEmployeeform.template
              .replace('%FNAME%', data[i].firstName)
              .replace('%LNAME%', data[i].lastName)
              .replace('%EMAIL%', data[i].email)
              .replace('%SALLARY%', data[i].sallary)
              .replaceAll('%ID%', data[i].id)

            let employeeEditIdentifiyerForm = formTemplates.editEmployeeform.identifiyer
              .replace('%PROFILE%', data[i].profilePic)

            if (data[i].role === 'admin') employeeEdittorForm = employeeEdittorForm.replace('%ROLEADMINSELECTER%', 'selected').replace('%ROLESELLERSELECTER%', '').replace('%ROLEMANUFACTURRERSELECTER%', '')
            else if (data[i].role === 'seller') employeeEdittorForm = employeeEdittorForm.replace('%ROLEADMINSELECTER%', '').replace('%ROLESELLERSELECTER%', 'selected').replace('%ROLEMANUFACTURRERSELECTER%', '')
            else if (data[i].role === 'manufacturrer') employeeEdittorForm = employeeEdittorForm.replace('%ROLEADMINSELECTER%', '').replace('%ROLESELLERSELECTER%', '').replace('%ROLEMANUFACTURRERSELECTER%', 'selected')

            if (data[i].gender === 'M') employeeEdittorForm = employeeEdittorForm.replace('%MALESELECTER%', 'checked').replace('%FEMALESELECTER%', '')
            else if (data[i].gender === 'F') employeeEdittorForm = employeeEdittorForm.replace('%MALESELECTER%', '').replace('%FEMALESELECTER%', 'checked')

            modalForms.innerHTML = employeeEdittorForm
            modalIdentifiyer.innerHTML = employeeEditIdentifiyerForm

            overlay.classList.remove('hidden');
            modal.classList.remove('hidden');
          }))

          employeeActionsDelete?.forEach((btn, i) => btn.addEventListener('click', () => {
            suspendActivateDelete(btn, i, 'empDelete', renderArray)
          }))
        })
      }


      // render(data);
      let filterdData = data;
      let searchData;

      const search = () => {
        if (employeeSearch.value) searchData = filterdData.filter(obj => obj.firstName?.includes(employeeSearch.value) || obj.lastName?.includes(employeeSearch.value))
        else searchData = filterdData

        if (searchData.length == 0) searchData = unknownArray

        render(searchData)
      }

      search()

      employeeFilter.addEventListener('change', () => {
        filterdData = data.filter(employee => employee.role === employeeFilter.value)
        if (filterdData.length == 0) filterdData = data;

        search()
      })

      employeeSearch.addEventListener('input', search)
    }

    else if (type === 'customers') {
      const render = (renderArray) => {
        let customerTemplate = '';

        renderArray.forEach((customer, i) => {
          customerTemplate += tableTemplate.customerTable.replace('%NUM%', i + 1)
            .replace('%DEVICE%', customer.device).replace('%EMAIL%', customer.email)
            .replaceAll('%STATUS%', customer.status).replace('%TITLE%', customer.status === 'active' ? 'Activate' : 'Suspend').replace('%TYPE%', customer.status === 'suspend' ? 'play' : 'pause')

          customerTableView.innerHTML = customerTemplate;
          // 
          const customerEdit = document.querySelectorAll('.customer_edit');
          customerEdit.forEach((btn, i) => btn.addEventListener('click', () => {
            //             %ID%
            // %EMAILVALUE%
            // %DEVICELIST%

            let customerEdittorForm = formTemplates.editCustomerForm.template
              .replace('%ID%', renderArray[i].id)
              .replace('%EMAILVALUE%', renderArray[i].email)
            // .replaceAll('%DEVICELIST%', deviceLists)

            // deviceLists = data.de              

            // customerEdittorForm.replace(`value="${renderArray[i].device}"`, `value="${renderArray[i].device}" selected`)

            let employeeEditIdentifiyerForm = formTemplates.editCustomerForm.identifiyer

            modalForms.innerHTML = customerEdittorForm
            modalIdentifiyer.innerHTML = employeeEditIdentifiyerForm

            overlay.classList.remove('hidden');
            modal.classList.remove('hidden');
          }))

          const customerActivateSuspend = document.querySelectorAll('.customer_activate_suspend');
          customerActivateSuspend.forEach((btn, i) => btn.addEventListener('click', () => {
            suspendActivateDelete(btn, i, 'customer', renderArray)
          }))
        })
      }


      let filterdData = data;
      let searchData;

      const search = () => {
        if (customerSearch.value) searchData = filterdData.filter(obj => obj.device?.includes(customerSearch.value) || obj.email?.includes(customerSearch.value))
        else searchData = filterdData

        if (searchData.length == 0) searchData = unknownArray

        render(searchData)
      }

      search()

      customerFilter.addEventListener('change', () => {
        filterdData = data.filter(device => device.status === customerFilter.value)
        if (filterdData.length == 0) filterdData = data;

        search()
      })

      customerSearch.addEventListener('input', search)
    }

    else if (type === 'transactions') {

      data.forEach((transaction, i) => {
        transactionBallance.textContent = `$${transaction.ballance}`;
        transactionCurrentPrice.textContent = `$${transaction.currentPrice}`;
        transactionEarn.textContent = `$${transaction.earn}`;
        transactionLoss.textContent = `$${transaction.loss}`;

        dashboardBallance.textContent = `$${transaction.ballance}`;
        dashboardCurrentPrice.textContent = `$${transaction.currentPrice}`;
      })

      const render = (renderArray) => {
        let transactionTemplate = '';

        renderArray.forEach((transaction, i) => {
          if (!transaction.ballance) {
            transactionTemplate += tableTemplate.transactionTable
              .replace('%NUM%', i + 1)
              .replace('%REASON%', transaction.reason)
              .replace('%DESCRIPTION%', transaction.description)
              .replace('%AMOUNT%', transaction.amount)
              .replace('%TYPE%', transaction.type)
              .replace('%KIND%', transaction.type === 'buy' ? 'up' : 'down')
          }

          transactionTableView.innerHTML = transactionTemplate;
        })
      }

      let filterdData = data;
      let searchData;

      const search = () => {
        if (transactionSearch.value) searchData = filterdData.filter(obj => obj.reason?.includes(transactionSearch.value) || obj.description?.includes(transactionSearch.value))
        else searchData = filterdData

        if (searchData.length == 0) searchData = unknownArray

        render(searchData)
      }

      search()

      transactionFilter.addEventListener('change', () => {
        filterdData = data.filter(device => device.type === transactionFilter.value)
        if (filterdData.length == 0) filterdData = data;

        search()
      })

      transactionSearch.addEventListener('input', search)
    }

    else if (type === 'deviceList') {

      data.forEach(list => {

        deviceLists += `<option value="${list}">${list}</option>`
      })

      if (document.querySelector('.device_list')) {
        document.querySelector('.device_list').innerHTML = '<option value="">--------------------------------------</option>' + deviceLists;
      };

    }

    else if (type === 'devicdAnalytics') {
      dashboardActiveDevices.textContent = data.activeDevices;
      dashboardDevices.textContent = data.allDevices;
      dashboardInactiveDevices.textContent = data.inactiveDevices;
      dashboardSuspendedDevices.textContent = data.suspendedDevices;
      dashboardCustomers.textContent = +data.activeDevices + data.suspendedDevices
    }

    else if (type === 'employeeAnlytics') {

      dashboardEmployees.textContent = data.allEmployees;
      dashboardManufacturrers.textContent = data.manufacturrers;
      dashboardSellers.textContent = data.sellers;
    }

    else if (type === 'feedback') {

      dashboardComments.textContent = data.length

      // console.log(!feedbackSearch.value);
      // console.log(feedbackFilter.value)

      const feedbackTemplate = (data) => {

        let feedbackTemplate = '';
        data.forEach(feedback => {

          feedbackTemplate += tableTemplate.feedbackList.replace('%EMAIL%', feedback.email)
            .replace('%SUBJECT%', feedback.subject).replace('%STATUS%', feedback.status).replace('%ID%', feedback.id)
        })

        feedbackList.innerHTML = feedbackTemplate;

        const lists = feedbackList.querySelectorAll('li');

        const fav = document.querySelector('.fav')
        const tes = document.querySelector('.tes')

        lists.forEach(list => {
          list.addEventListener('click', () => {
            lists.forEach(list => list.classList.remove('li-active'))

            const feedback = data.filter(feedback => +feedback.id === +list.id)
            list.classList.add('li-active')
            feedbackMsg.textContent = feedback[0].message;

            feedback[0].favorite == 'true' ? fav.classList.add('feedback-fav') : fav.classList.remove('feedback-fav')
            feedback[0].testimonial == 'true' ? tes.classList.add('feedback-tes') : tes.classList.remove('feedback-tes')

            if (feedback[0].status === "unreaded") {
              (async () => {
                const response = await fetch('admin.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },

                  body: `identify=unread&item=${feedback[0].id}`
                });
                // const data = await response.json();

              })()

              window.location.href = `${window.location.href.slice(0, 38)}#${feedback[0].id}`;
            }
          })

          const id = window.location.hash.slice(1);

          if (+id === +list.id) {
            const feedback = data.filter(feedback => +feedback.id === +list.id)
            list.classList.add('li-active')
            feedbackMsg.textContent = feedback[0].message;

            feedback[0].favorite == 'true' ? fav.classList.add('feedback-fav') : fav.classList.remove('feedback-fav')
            feedback[0].testimonial == 'true' ? tes.classList.add('feedback-tes') : tes.classList.remove('feedback-tes')
          }
        })

      }

      // const dataz = [
      //   {
      //     "id": "2",
      //     "email": "kirubelbewket@gmail.com",
      //     "subject": "deviceThree",
      //     "message": "wow!, it is a greatest technology i ever seen, and am glad it is made by my country",
      //     "favorite": "true",
      //     "testimonial": "true",
      //     "status": "readed",
      //     "date": "2024-05-05"
      //   },
      //   {
      //     "id": "6",
      //     "email": "dagimbirhan@gmail.com",
      //     "subject": "deviceOne",
      //     "message": "am trying to control my home remotely",
      //     "favorite": "false",
      //     "testimonial": "true",
      //     "status": "readed",
      //     "date": "2024-05-05"
      //   }
      // ]


      let feedData = data;
      let filterData;

      const search = () => {
        if (feedbackSearch.value) filterData = feedData.filter(fltr => fltr.subject.includes(feedbackSearch.value) || fltr.email.includes(feedbackSearch.value))
        else filterData = feedData;

        // console.log(filterData);
        feedbackTemplate(filterData);
      }

      search();

      feedbackFilter.addEventListener('input', () => {

        let temp = data.flatMap(fmap => {
          switch (feedbackFilter.value) {
            case 'Foverites':
              if (fmap.favorite === 'true') return fmap;
              break;
            case 'Testimonial':
              if (fmap.testimonial === 'true') return fmap;
              break;
            case 'Unreaded':
              if (fmap.status === 'unreaded') return fmap;
              break;
            default: return fmap;
          }
        });

        // console.log(temp)

        feedData = temp.filter(filter => filter)
        // feedbackTemplate(check.filter(filter => filter));
        search();
      })

      feedbackSearch.addEventListener('input', search);

    }

  }

  function updateOldData(oldDataValue, dataValue, type) {
    for (const key in dataValue) {
      if (oldDataValue.hasOwnProperty(key)) {
        if (!valuesAreEqual(oldDataValue[key], dataValue[key])) {
          oldDataValue[key] = dataValue[key];

          templateMaker(oldDataValue, type)
        }
      } else {
        oldDataValue[key] = dataValue[key];

        templateMaker(oldDataValue, type)
      }
    }

    for (const key in oldDataValue) {
      if (!dataValue.hasOwnProperty(key)) {
        delete oldDataValue[key];

        templateMaker(oldDataValue, type)
      }
    }

    return oldDataValue;
  }

  function valuesAreEqual(value1, value2) {
    if (typeof value1 === 'object' && typeof value2 === 'object') {
      return JSON.stringify(value1) === JSON.stringify(value2);
    }
    return value1 === value2;
  }

  try {
    const response = await fetch('admin.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: "identify=pageInfo"

    });
    const data = await response.json();
    // const data = await response.text();
    // console.log(data);


    if (Object.keys(oldData).length === 0) {
      oldData = data;

      templateMaker(oldData.deviceList, 'deviceList')
      templateMaker(oldData.customers, 'customers')
      templateMaker(oldData.employees, 'employees')
      templateMaker(oldData.devices, 'devices')
      templateMaker(oldData.transactions, 'transactions')
      templateMaker(oldData.feedback, 'feedback')

      templateMaker(oldData.dashboard.devicdAnalytics, 'devicdAnalytics')
      templateMaker(oldData.dashboard.employeeAnlytics, 'employeeAnlytics')
    }

    // #fff device list

    oldData.deviceList.forEach(device => {
      if (!data.deviceList.includes(device)) {
        oldData.deviceList = data.deviceList;
        templateMaker(oldData.deviceList, 'deviceList')
      }
    })

    data.deviceList.forEach(device => {
      if (!oldData.deviceList.includes(device)) {
        oldData.deviceList = data.deviceList;
        templateMaker(oldData.deviceList, 'deviceList')
      }
    })

    // #fff device

    updateOldData(oldData.devices, data.devices, 'devices');

    // #fff customer

    updateOldData(oldData.customers, data.customers, 'customers');

    // #fff employee

    updateOldData(oldData.employees, data.employees, 'employees');

    // #fff feedback

    updateOldData(oldData.feedback, data.feedback, 'feedback');

    // #fff transaction

    updateOldData(oldData.transactions, data.transactions, 'transactions');

    // #fff analytic

    updateOldData(oldData.dashboard.devicdAnalytics, data.dashboard.devicdAnalytics, 'devicdAnalytics')
    updateOldData(oldData.dashboard.employeeAnlytics, data.dashboard.employeeAnlytics, 'employeeAnlytics')

  } catch (error) {
    console.log(error);
  }

}

pageUpdater();
const pageUpdaterInterval = setInterval(pageUpdater, 500);

// ================================================================//

const displayChanger = (type) => {
  if (type == 'Dashboard') {
    main.forEach(itm => itm.classList.add('hidden'))
    dashboardMain.classList.remove('hidden')
  };

  if (type == 'Devices') {
    main.forEach(itm => itm.classList.add('hidden'))
    deviceMain.classList.remove('hidden')
  };

  if (type == 'Employees') {
    main.forEach(itm => itm.classList.add('hidden'))
    employeeMain.classList.remove('hidden')
  };

  if (type == 'Customers') {
    main.forEach(itm => itm.classList.add('hidden'))
    customerMain.classList.remove('hidden')
  };

  if (type == 'Feedbacks') {
    main.forEach(itm => itm.classList.add('hidden'))
    feedbackMain.classList.remove('hidden')
  };

  if (type == 'Transactions') {
    main.forEach(itm => itm.classList.add('hidden'))
    transactionMain.classList.remove('hidden')
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
});
buttons.forEach(btn => {
  btn.addEventListener('click', () => {
    if (btn.textContent === "Add Device") {
      modalForms.innerHTML = formTemplates.addDeviceForm.template;
      modalIdentifiyer.innerHTML = formTemplates.addDeviceForm.identifiyer;

    } else if (btn.textContent === "Add Employee") {
      modalForms.innerHTML = formTemplates.addEmployeeForm.template;
      modalIdentifiyer.innerHTML = formTemplates.addEmployeeForm.identifiyer

    } else if (btn.textContent === "Sell Device") {

      modalForms.innerHTML = formTemplates.addCustomerForm.template.replace('%DEVICELIST%', deviceLists);
      modalIdentifiyer.innerHTML = formTemplates.addCustomerForm.identifiyer;

    } else if (btn.textContent === "Add Transaction") {
      modalForms.innerHTML = formTemplates.addTransactionForm.template;
      modalIdentifiyer.innerHTML = formTemplates.addTransactionForm.identifiyer;
    }

    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
  })
})

overlay.addEventListener('click', () => {
  overlay.classList.add('hidden')
  modal.classList.add('hidden')
  changePasswordModal.classList.add('hidden')
})

modalCloseBtn.addEventListener('click', () => {
  overlay.classList.add('hidden')
  modal.classList.add('hidden')
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
    const response = await fetch('admin.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams(formDataObject)
    });
    const data = await response.json();
    // console.log(data)

    // Handle the response from the server

    if (data.status === 'success') {

      loadAnimContainer.remove();

      let type = 'success';
      let icon = 'las la-check';
      let title = 'Success';
      // let text = 'success!👍 server saved the data.';
      let text = data.message;
      createToast(type, icon, title, text);
    } else if (data.status === 'warning') {

      loadAnimContainer.remove();

      let type = 'warning';
      let icon = 'las la-exclamation';
      let title = 'Error';
      let text = data.message;
      createToast(type, icon, title, text);

    } else {

      loadAnimContainer.remove();

      let type = 'error';
      let icon = 'las la-exclamation';
      let title = 'Error';
      let text = data.message;
      createToast(type, icon, title, text);

    }
  } catch {
    loadAnimContainer.remove();
    // console.log('error');
    let type = 'error';
    let icon = 'las la-exclamation';
    let title = 'Error';
    let text = "Error!💥 faild to send to the server";
    createToast(type, icon, title, text);
  }
});

// #fff logout function

logoutBtn.addEventListener('click', async () => {
  const response = await fetch('admin.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: "logout=logout"
  });
  const data = await response.json();
  if (data.message === 'logedout') location.reload();
})

// console.log(feedbackActions)
feedbackActions.forEach(feedbackAction => feedbackAction.addEventListener('click', (e) => {
  if (e.target.title) {
    console.log(feedbackAction)
    if (feedbackList.querySelector('.li-active')?.id) {

      const id = feedbackList.querySelector('.li-active').id;

      (async () => {
        const res = await fetch('admin.php', {
          method: 'POST',
          headers: { 'content-type': 'application/x-www-form-urlencoded' },
          body: `identify=${e.target.title}&item=${id}`
        })

        const data = await res.json();
        const currentFeed = JSON.parse(data.message).feedback.find(feed => feed.id == id);
        // console.log(currentFeed.favorite, currentFeed.testimonial)

        const fav = document.querySelector('.fav')
        const tes = document.querySelector('.tes')

        if (e.target.title == 'Add to Favorites') {
          currentFeed.favorite == 'true' ? fav.classList.add('feedback-fav') : fav.classList.remove('feedback-fav')
          window.location.href = `${window.location.href.slice(0, 38)}#${id}`;
        }
        else if (e.target.title == 'Share to Testimonial') {
          currentFeed.testimonial == 'true' ? tes.classList.add('feedback-tes') : tes.classList.remove('feedback-tes')
          window.location.href = `${window.location.href.slice(0, 38)}#${id}`;
        }

      })()

    }
  }
}))

changePassword.addEventListener('click', () => {
  // overlay.classList.remove('hidden');
  // changePasswordModal.classList.remove('hidden')

  modalForms.innerHTML = formTemplates.changePasswordForm.template;
  modalIdentifiyer.innerHTML = formTemplates.changePasswordForm.identifiyer;
})

// changePasswordModalX.addEventListener('click', () => {
//   overlay.classList.add('hidden');
//   changePasswordModal.classList.add('hidden')
// })

document.querySelector('.notify-icon')?.addEventListener('click', () => window.location = 'customer.php')