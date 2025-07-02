import React from 'react'
import { Alert } from 'react-bootstrap'

const InstructionMessage = ({validations}) => {
    
  return (
    <Alert variant="danger" style={{
        fontSize: '1.2rem',
    }}>
        Your uploaded validation images have been declined for the following reason:<br />
        <strong>{validations.message}</strong>
    </Alert>
  )
}

export default InstructionMessage
