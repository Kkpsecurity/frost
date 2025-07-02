import React from "react";
import { Container, Row, Col, Card, Alert } from "react-bootstrap";
import VideoPlayer from "../../Video/VideoPlayer";
import "./video.css";
import { ClassDataShape, StudentType } from "../../../../../../Config/types";


const VideosStudyRoom = ({
    data,
    student,
    selectedLessonId,
    createTrackLesson,
    debug = false,
}: {
    data: ClassDataShape
    student: StudentType
    selectedLessonId: number
    createTrackLesson: (e: React.MouseEvent<HTMLButtonElement>) => void
    debug: boolean
}) => {

    return (<>  </>);
}



// const FrostLessonPlayer = ({ lesson_id }: { lesson_id: number }) => {
//     return <VideoPlayer lesson_id={lesson_id} />;
// };

// const VideoRoomMessage = ({
//     createTrackLesson,
    
// }: {
//     createTrackLesson: (e: React.MouseEvent<HTMLButtonElement>) => void;
// }) => {
//     return (
//         <Col md={12}>
//             <Alert variant="info">
//                 <Alert.Heading>
//                     <i className="fa fa-play mr-2" /> Select a lesson to begin
//                 </Alert.Heading>
//                 <p>
//                     The Offline class is designed to help make up any of the
//                     lessons you have missed during the live class. Please select
//                     a lesson to begin.
//                     <i>
//                         Note: Only lessons that are eligible for the offline
//                         class will be shown. <br />
//                         <button
//                             id={0}
//                             className="btn btn-primary"
//                             onClick={createTrackLesson}
//                             aria-label="Begin offline session"
//                         >
//                             Begin Offline Session
//                         </button>
//                     </i>
//                 </p>
//             </Alert>
//         </Col>
//     );
// };

// interface Props {
//     data: ClassDataShape;
//     student: StudentType;
//     selectedLessonId: number;
//     createTrackLesson: (e: React.MouseEvent<HTMLButtonElement>) => void;
//     debug: boolean;
// }

// const VideosStudyRoom: React.FC<Props> = ({
//     data,
//     student,
//     selectedLessonId,
//     createTrackLesson,
//     debug = false,
// }) => {
//     const { course, courseLessons } = data;

//     if (!course) {
//         return (
//             <Alert variant="danger">
//                 No courses found. Please contact your instructor.
//             </Alert>
//         );
//     }

//     return (
//         <Container>
//             <Row className="mt-2">
//                 {selectedLessonId ? (
//                     <FrostLessonPlayer lesson_id={selectedLessonId} />
//                 ) : (
//                     <VideoRoomMessage createTrackLesson={createTrackLesson} />
//                 )}
//             </Row>
//         </Container>
//     );
// };

export default VideosStudyRoom;

// <Row>
//           <Col lg={8} md={12}>
{
    /* <div className="embed-responsive embed-responsive-16by9 mt-2">
<Card>
    <Card.Header className="d-flex align-content-center">
        <span
            style={{
                fontSize: "1.5rem",
                fontWeight: "bold",
            }}
        >
            <i className="fa fa-play mr-2" />{" "}
            {course.title_long}
        </span>
    </Card.Header>
    <VideoPlayer />
    <Card.Footer>
        <Card.Title className="text-dark">
            <i className="fa fa-play mr-2" /> lesson
            name
        </Card.Title>

        <Card.Text>
            Lesson Progress --- Time of lesson --- My
            Progress
        </Card.Text>
    </Card.Footer>
</Card>
</div>
</Col>      <Col lg={8} md={12}> */
}
//                     <div className="embed-responsive embed-responsive-16by9">
//                         <Card>

//
//                             <VideoPlayer />

//                         </Card>
//                     </div>
//                 </Col>
//                 <Col lg={4} md={12}>
//                     <VideoTrackPlaylist />

//                     <div className="d-md-none">
//                         <Card>
//                             <Card.Body>
//                                 <Card.Title>Video Playlist</Card.Title>
//                                 <ListGroup>
//                                     <ListGroup.Item action>
//                                         Video 1
//                                     </ListGroup.Item>
//                                     <ListGroup.Item action>
//                                         Video 2
//                                     </ListGroup.Item>
//                                     <ListGroup.Item action>
//                                         Video 3
//                                     </ListGroup.Item>
//                                 </ListGroup>
//                             </Card.Body>
//                         </Card>
//                     </div>
//                 </Col>
//             </Row>
