/**
 * Laravel Props Utils - Utilities for reading Laravel data from DOM
 * Provides safe access to data-* attributes set by Laravel blade templates
 */

import { 
    LaravelPropsData, 
    StudentDashboardData,
    ClassDashboardData,
    LaravelPropsValidator 
} from '../types/LaravelProps';

export class LaravelPropsReader {
    /**
     * Read student dashboard data from the student-props DOM element
     */
    static readStudentProps(): StudentDashboardData | null {
        try {
            console.log('🔍 Reading student props from DOM...');
            
            // Find the student-props element (now a script tag)
            const studentPropsElement = document.getElementById('student-props');
            if (!studentPropsElement) {
                console.error('❌ Student props element not found in DOM');
                return null;
            }

            console.log('✅ Student props element found:', studentPropsElement);

            // Read the text content from the script tag
            const studentDataRaw = studentPropsElement.textContent?.trim();
            if (!studentDataRaw) {
                console.error('❌ No text content found in student-props script tag');
                return null;
            }

            console.log('📋 Raw student data:', studentDataRaw);

            // Parse JSON
            let studentDataParsed: any;
            try {
                studentDataParsed = JSON.parse(studentDataRaw);
                console.log('✅ Parsed student data:', studentDataParsed);
            } catch (parseError) {
                console.error('❌ Failed to parse student data JSON:', parseError);
                console.error('❌ Raw data was:', studentDataRaw);
                return null;
            }

            // Validate the data structure
            if (!LaravelPropsValidator.validateStudentDashboardData(studentDataParsed)) {
                console.error('❌ Student data validation failed');
                console.error('❌ Invalid data:', studentDataParsed);
                
                // Return default data instead of null
                console.log('🔄 Using default student data as fallback');
                return LaravelPropsValidator.getDefaultStudentData();
            }

            console.log('✅ Successfully read student props:', studentDataParsed);
            return studentDataParsed as StudentDashboardData;

        } catch (error) {
            console.error('❌ Error reading student props:', error);
            return null;
        }
    }

    /**
     * Read class dashboard data from the class-props DOM element
     */
    static readClassProps(): ClassDashboardData | null {
        try {
            console.log('🔍 Reading class props from DOM...');
            
            // Find the class-props element
            const classPropsElement = document.getElementById('class-props');
            if (!classPropsElement) {
                console.error('❌ Class props element not found in DOM');
                return null;
            }

            console.log('✅ Class props element found:', classPropsElement);

            // Read class dashboard data
            const classDataRaw = classPropsElement.getAttribute('class-dashboard-data');
            if (!classDataRaw) {
                console.error('❌ class-dashboard-data attribute not found');
                return null;
            }

            console.log('📋 Raw class data:', classDataRaw);

            // Parse JSON
            let classDataParsed: any;
            try {
                classDataParsed = JSON.parse(classDataRaw);
                console.log('✅ Parsed class data:', classDataParsed);
            } catch (parseError) {
                console.error('❌ Failed to parse class data JSON:', parseError);
                console.error('❌ Raw data was:', classDataRaw);
                return null;
            }

            // Validate the data structure
            if (!LaravelPropsValidator.validateClassDashboardData(classDataParsed)) {
                console.error('❌ Class data validation failed');
                console.error('❌ Invalid data:', classDataParsed);
                
                // Return default data instead of null
                console.log('🔄 Using default class data as fallback');
                return LaravelPropsValidator.getDefaultClassData();
            }

            console.log('✅ Successfully read class props:', classDataParsed);
            return classDataParsed as ClassDashboardData;

        } catch (error) {
            console.error('❌ Error reading class props:', error);
            return null;
        }
    }

    /**
     * Read both student and class data plus course auth ID
     */
    static readAllProps(): LaravelPropsData {
        // Get course auth ID from either element (both should have it)
        let courseAuthId: string | null = null;
        
        const studentPropsElement = document.getElementById('student-props');
        if (studentPropsElement) {
            courseAuthId = studentPropsElement.getAttribute('data-course-auth-id');
        }

        const studentData = this.readStudentProps();
        const classData = this.readClassProps();
        
        return {
            courseAuthId,
            studentData: studentData || LaravelPropsValidator.getDefaultStudentData(),
            classData: classData || LaravelPropsValidator.getDefaultClassData()
        };
    }

    /**
     * Get safe student data - either from Laravel or fallback to defaults
     */
    static getSafeStudentData(): StudentDashboardData {
        const studentData = this.readStudentProps();
        
        if (studentData) {
            console.log('✅ Using Laravel student data');
            return studentData;
        }

        console.log('🔄 Using default student data');
        return LaravelPropsValidator.getDefaultStudentData();
    }

    /**
     * Get safe class data - either from Laravel or fallback to defaults
     */
    static getSafeClassData(): ClassDashboardData {
        const classData = this.readClassProps();
        
        if (classData) {
            console.log('✅ Using Laravel class data');
            return classData;
        }

        console.log('🔄 Using default class data');
        return LaravelPropsValidator.getDefaultClassData();
    }

    /**
     * Debug function to log all available data attributes
     */
    static debugAllDataAttributes(): void {
        console.log('🔍 Debugging all data attributes...');
        
        const propsElement = document.getElementById('props');
        if (!propsElement) {
            console.error('❌ Props element not found');
            return;
        }

        console.log('📋 Props element attributes:');
        for (let i = 0; i < propsElement.attributes.length; i++) {
            const attr = propsElement.attributes[i];
            console.log(`  - ${attr.name}: ${attr.value}`);
        }

        // Also check all elements with data attributes
        console.log('📋 All elements with data attributes:');
        const allDataElements = document.querySelectorAll('[data-dashboard-data]');
        allDataElements.forEach((element, index) => {
            console.log(`  Element ${index}:`, element);
            console.log(`    ID: ${element.id}`);
            console.log(`    data-dashboard-data: ${element.getAttribute('data-dashboard-data')}`);
        });
    }
}
