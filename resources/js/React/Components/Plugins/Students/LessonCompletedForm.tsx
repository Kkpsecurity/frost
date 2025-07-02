import { useState } from "react";
import { useForm,  } from "react-hook-form";

const LessonCompletedForm = () => {
    const [lessonUpdate, setLessonUpdate] = useState('');
  
    const handleSubmit = (event) => {
      event.preventDefault();
      console.log('Lesson update submitted: ' + lessonUpdate);
      // Add code here to submit the form data to the server/database
    }
  
    const handleInputChange = (event) => {
      setLessonUpdate(event.target.value);
    }

    const confirmEndOfLesson = (data) => {};

    const methods = useForm();
  
    return (
      <div>
        <h2>Lesson Completed</h2>
        <form onSubmit={methods.handleSubmit(confirmEndOfLesson)}>
          <label>
            The Current lesson has ended and must be updated 
            to reflect your progress. failuar to do so will result in a 
            failed lesson.            
          </label>
          <br />
          <button type="submit">Submit</button>
        </form>
      </div>
    );
  }
  
  export default LessonCompletedForm;
  