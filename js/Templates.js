export const formTemplates = {
  "addDeviceForm": {
    "template": `
    <input type="hidden" name="identify" value="addDevice">
        <div class="modal-form-content">
          <span>Device Name: </span
          ><input type="text" name="deviceName" id="" class="modal-form-input" />
        </div>`,

    "identifiyer": `<span class="las la-inbox modal-icon"></span>
        <div class="modal-type">Add Device</div>`,
  },
  "addEmployeeForm": {
    "template": `
      <input type="hidden" name="identify" value="addEmployee">
        <div class="modal-form-content">
          <span>First Name: </span
          ><input type="text" name="firstName" id="" class="modal-form-input" />
        </div>
        <div class="modal-form-content">
          <span>Last Name: </span
          ><input type="text" name="lastName" id="" class="modal-form-input" />
        </div>
        <div class="modal-form-content">
          <span>Email: </span
          ><input type="email" name="email" id="" class="modal-form-input" />
        </div>
        <div class="modal-form-content">
          <span>Password: </span
          ><input type="text" name="password" id="" class="modal-form-input" />
        </div>
        <div class="modal-form-content">
          <span>Sallary: </span>
          <input type="number" min="0" name="sallary" id="" class="modal-form-input" />
        </div>
        <div class="modal-form-content">
          <span>Gender: </span>
          <span class="gender-radio">
            <input type="radio" name="gender" id="male" value="M" /> <label for="male">Male</label>
            <input type="radio" name="gender" id="female" value="F" /> <label for="female">Female</label></span
          >
        </div>
        <div class="modal-form-content">
          <span>Role: </span>
          <select name="role" id="" class="modal-form-input">
            <option value="">----------------------------------------</option>
            <option value="admin">Admin</option>
            <option value="manufacturrer">Manufacturrer</option>
          </select>
        </div>`,

    "identifiyer": `<span class="las la-user-alt modal-icon"></span>
        <div class="modal-type">Add Employee</div>`
  },
  "addCustomerForm": {
    "template": `<input type="hidden" name="identify" value="addCustomer">
            <div class="modal-form-content">
                <span>Email: </span><input type="email" name="email" id="" class="modal-form-input" />
            </div>
            <div class="modal-form-content">
                <span>Password: </span><input type="text" name="password" id="" class="modal-form-input" />
            </div>
    
                <div class="modal-form-content">
                    <span>Device: </span>
                    <select name="deviceName" id="" class="modal-form-input device_list">
                        %DEVICELIST%
                    </select>
                </div>`,
    "identifiyer": `<span class="las la-users modal-icon"></span>
       <div class="modal-type">Add Customer</div>`
  },
  "addTransactionForm": {
    "template": `<input type="hidden" name="identify" value="addTransaction">
        <div class="modal-form-content">
          <span>Amount: </span
          ><input type="number" name="price" id="" class="modal-form-input" />
        </div>
        <div class="modal-form-content">
          <span>Reason: </span
          ><input type="text" name="reason" id="" class="modal-form-input" />
        </div>
        <div class="modal-form-content">
        <span>Description: </span><input type="text" name="description" id="" class="modal-form-input">
        </div>
        <div class="modal-form-content">
          <span>Type: </span>
          <select name="type" id="" class="modal-form-input">
            <option value="">
              ----------------------------------------
            </option>
            <option value="sell">Erning</option>
            <option value="buy">Loss</option>
          </select>
        </div>`,
    "identifiyer": `<span class="las la-exchange-alt modal-icon"></span>
        <div class="modal-type">Add Transaction</div>`
  },
  "updatePriceForm": {
    "template": `<input type="hidden" name="identify" value="updatePrice">
    <div class="modal-form-content">
    <span>Amount: </span><input type="number" name="price" id="" class="modal-form-input">
    </div>`,
    "identifiyer": `<span class="las la-money-bill-wave modal-icon"></span>
    <div class="modal-type">Change Current Price</div>`
  },
  "editEmployeeform": {
    "template": `
      <input type="hidden" name="identify" value="editEmployee">
      <input type="hidden" name="id" value="%ID%">
        <div class="modal-form-content">
          <span>First Name: </span
          ><input type="text" name="firstName" id="" value="%FNAME%" class="modal-form-input" />
        </div>
        <div class="modal-form-content">
          <span>Last Name: </span
          ><input type="text" name="lastName" value="%LNAME%" id="" class="modal-form-input" />
        </div>
        <div class="modal-form-content">
          <span>Email: </span
          ><input type="email" name="email" value="%EMAIL%" id="" class="modal-form-input" />
        </div>
        <div class="modal-form-content">
          <span>Sallary: </span>
          <input type="number" min="0" value="%SALLARY%" name="sallary" id="" class="modal-form-input" />
        </div>
        <div class="modal-form-content">
          <span>Gender: </span>
          <span class="gender-radio">
            <input type="radio" name="gender" %MALESELECTER% id="male" value="M" /> <label for="male">Male</label>
            <input type="radio" name="gender" %FEMALESELECTER% id="female" value="F" /> <label for="female">Female</label></span
          >
        </div>
        <div class="modal-form-content">
          <span>Role: </span>
          <select name="role" id="" class="modal-form-input">
            <option %ROLEADMINSELECTER% value="admin">Admin</option>
            <option %ROLEMANUFACTURRERSELECTER% value="manufacturrer">manufacturrer</option>
          </select>
        </div>`,

    "identifiyer": `<div class="pp-img" style=" background-image: url('%PROFILE%')"></div>
        <div class="modal-type">Edit Employee</div>`
  },
  "editCustomerForm": {
    "template": `<input type="hidden" name="identify" value="editCustomer">
            <input type="hidden" name="id" value="%ID%">
            <div class="modal-form-content">
                <span>Email: </span><input type="email" name="email" value="%EMAILVALUE%" id="" class="modal-form-input customerEmail" />
            </div>`,
    "identifiyer": `<span class="las la-users modal-icon"></span>
      <div class="modal-type">Edit Customer</div>`
  },
  "changePasswordForm": {
    "template": `
      <input type="hidden" name="identify" value="Change Password">
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

export const tableTemplate = {
  "deviceTable": `<tr>
          <td>#%NUM% </td>
          <td>
              <div class="client">
                  <div class="client-info">
                      <h4> %DEVICE% </h4>
                      <small> %BUYYER% </small>
                  </div>
              </div>
          </td>
          <td>$ %PRICE% </td>
          <td>
              <span class=" %STATUS% "> %STATUS% </span>
          </td>
          <td>
              <div class="actions">
                  
                  <span title=" %TITLE%" class="las la-%ACTION% device_action_delete"></span>
                  <!--%DELETE%-->
              </div>
          </td>
      </tr>`,

  "employeeTable": `<tr>
        <td>#%NUM%</td>
        <td>
            <div class="client">
                <div class="client-info employee-list">
                    <h4>
                      <div class="pp-img" style=" background-image: url('%PROFILE%')"></div>
                      %FIRSTNAME% %LASTNAME%
                    </h4>
                </div>
            </div>
        </td>
        <td>%SALLARY%</td>
        <td>
            <span class="employee-role">%ROLE%</span>
        </td>
        <td>
            <div class="actions">
              <span title="Edit" class="las la-pen employee_action_edit"></span>
              <span title="Fire" id="%ID%" class="las la-trash employee_action_delete"></span>
            </div>
        </td>
      </tr>`,

  "customerTable": `<tr>
      <td>#%NUM%</td>
      <td>
          <div class="client">
              <div class="client-info">
                  <h4>%EMAIL%</h4>
                  <small>%DEVICE%</small>
              </div>
          </div>
      </td>
      <td>
          <span class="%STATUS%">%STATUS%</span>
      </td>
      <td>
          <div class="actions">
              <span title="Edit" class="las la-pen customer_edit"></span>
              <span title=" %TITLE%" class="las la-%TYPE% customer_activate_suspend"></span>
          </div>
      </td>
    </tr>`,

  "transactionTable": `<tr>
    <td>#%NUM%</td>
    <td>
        <div class="client">
            <div class="client-info">
                <h4>%REASON%</h4>
                <small>%DESCRIPTION%</small>
            </div>
        </div>
    </td>
    <td>%AMOUNT%</td>
    <td>
        <span class="las la-arrow-%KIND% %TYPE%"></span>
    </td>
  </tr>`,

  "feedbackList": `<li id="%ID%">
  <h4>%SUBJECT%</h4>
  <small>%EMAIL%</small>
  <div class="%STATUS%"></div>
</li>`
};
// exports.tableTemplate = tableTemplate;
