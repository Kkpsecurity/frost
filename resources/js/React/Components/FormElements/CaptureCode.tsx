import { useState, useEffect } from "react";

const CaptureCode = () => {
    const [numbers, setNumbers] = useState<number[]>([]);

    const randomNumberInRange = (min, max) => {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    const initiateChallenge = () => {
        const generatedNumbers = [];
        for (let i = 0; i < 4; i++) {
            generatedNumbers.push(randomNumberInRange(0, 9));
        }

        localStorage.setItem('generatedNumbers', JSON.stringify(generatedNumbers));
        setNumbers(generatedNumbers);
    };

    useEffect(() => {
        initiateChallenge();
    }, []);

    return (
        <div className="d-flex justify-content-center align-items-center">
            <div id="numbers" style={{
                fontSize: "36px",
                width: "100px",
                height: "40px",
                fontWeight: "bold",
                color: "#000",
                textTransform: "uppercase",
                fontFamily: "Arial, Helvetica, sans-serif",
            }}>
                {numbers.map((number, index) => (
                    <span key={index}>{number}</span>
                ))}
            </div>
        </div>
    );
}

export default CaptureCode;

