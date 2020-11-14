export class Validator {

  constructor(input) {
    this.input = input;
    this.error = '';
    this.fields = [];
  }

  validate() {
    if (this.fields.every(field => {
      if (field.name !== this.input.name) {
        return true;
      } else {
        const value = this.input.value.trim();

        if (!value) {
          this.error = `${field.name} cannot be empty.`;
        } else if (!field.regex.test(value)) {
          this.error = field.message;
        };

        return false;
      }
    })) {
      this.error = `There is no validator for this input`
    }

    return this.error;
  }

  setFields(fields) {
    this.fields = fields;
  }

  addFields(fields) {
    this.fields = {
      ...this.fields,
      fields
    }
  }
}

export class ManagerValidator extends Validator {
  fields = [
    {
      name: 'fullname',
      regex: /^[A-Za-z ]{1,50}$/,
      message: 'fullname must be alphabetic (a-z or A-Z).'
    },
    {
      name: 'username',
      regex: /^[A-Za-z0-9]{4,20}$/,
      message: 'username must be 4-20 characters and alphanumeric.'
    },
    {
      name: 'password',
      regex: /^[A-Za-z0-9]{4,20}$/,
      message: 'password must be 4-20 characters and alphanumeric.'
    },
    {
      name: 'email',
      regex: /^\w+@\w+\.\w+$/,
      message: 'please enter a valid email (e.g., John_Doe@gmail.com)'
    }
  ];
}

export class UserValidator extends Validator {
  fields = [
    {
      name: 'username',
      regex: /^[A-Za-z0-9]{4,20}$/,
      message: 'username must be 4-20 characters and alphanumeric.'
    },
    {
      name: 'password',
      regex: /^[A-Za-z0-9]{4,20}$/,
      message: 'password must be 4-20 characters and alphanumeric.'
    },
    {
      name: 'email',
      regex: /^\w+@\w+\.\w+$/,
      message: 'please enter a valid email (e.g., John_Doe@gmail.com)'
    }
  ];
}
export class ClientValidator extends Validator {
  fields = [
    {
      name: 'fullname',
      regex: /^[A-Za-z ]{1,50}$/,
      message: 'lastname must be alphabetic (a-z or A-Z).'
    },
    {
      name: 'phone',
      regex: /^[0-9]{8,16}$/,
      message: 'phone must be numeric (8-16 numbers)'
    },
    {
      name: 'email',
      regex: /^\w+@\w+\.\w+$/,
      message: 'please enter a valid email (e.g., John_Doe@gmail.com)'
    }
  ];
}